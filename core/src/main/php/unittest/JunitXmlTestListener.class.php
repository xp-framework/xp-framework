<?php
/* This class is part of the XP framework
 *
 * $Id: XmlTestListener.class.php 13822 2009-11-12 14:55:15Z friebe $
 */

  uses(
    'unittest.TestListener', 
    'io.streams.OutputStreamWriter', 
    'xml.Tree',
    'util.collections.HashTable'
  );

  /**
   * Creates an XML file suitable for importing into Netbeans
   *
   * @test     None yet
   * @purpose  TestListener
   */
  class JunitXmlTestListener extends Object implements TestListener {
    public $out= NULL;
    protected $tree= NULL;
    protected $classes= NULL;
    protected $overalltime= 0;
    protected $wrappernode= NULL;


    /**
     * Constructor
     *
     * @param   io.streams.OutputStreamWriter out
     */
    public function __construct(OutputStreamWriter $out) {
      $this->out= $out;
      $this->tree= new Tree('testsuites');
      
      // Add a wrapper node because Netbeans expects it
      $this->wrapperNode=  $this->tree->addChild(new Node('testsuite', NULL, array(
          'name'       => '.',
          'tests'      => 0,
          'assertions' => 0,
          'failures'   => 0,
          'errors'     => 0,
          'skipped'    => 0,
          'time'       => 0.0
        )));
      
      $this->classes= create('new util.collections.HashTable<lang.XPClass, xml.Node>()');
    }

    /*
     * Tries to get class uri via reflection
     *
     * @param lang.XPClass $class
     * @return string
     */
    private function getFileUri(XPClass $class) {
      try {
        $loader= $class->getClassLoader();
        $Urimethod= $loader->getClass()->getMethod('classURI');
        $Urimethod->setAccessible(TRUE);
        return $Urimethod->invoke($loader, $class->getName());
      } catch (Exception $ignored) {
        return '';
      }
    }

        /*
     * Tries to get method start line
     *
     * @param lang.XPClass $class
     * @param string $methodname
     * @return int
     */
    private function getStartLine(XPClass $class, $methodname) {
      try {
        return $class->_reflect->getMethod($methodname)->getStartLine();
      } catch (Exception $ignored) {
        return 0;
      }
    }
    
    /**
     * Called when a test case starts.
     *
     * @param   unittest.TestCase failure
     */
    public function testStarted(TestCase $case) {
      // NOOP
    }

    /**
     * Add test result node, if it does not yet exist.
     *
     * @param   unittest.TestCase case
     * @return  xml.Node
     */
    protected function testNode(TestCase $case) {
      $class= $case->getClass();
      if (!$this->classes->containsKey($class)) {
        $this->classes[$class]= $this->wrapperNode->addChild(new Node('testsuite', NULL, array(
          'name'       => $class->getName(),
          'file'       => $this->getFileUri($class),
          'fullPackage'=> $class->getPackage()->getName(),
          'package'    => $class->getPackage()->getSimpleName(),
          'tests'      => 0,
          'assertions' => 0,
          'failures'   => 0,
          'errors'     => 0,
          'skipped'    => 0,
          'time'       => 0.0
        )));
      }

      return $this->classes[$class];
    }
    
    /**
     * Add test case information node and update suite information.
     *
     * @param   unittest.TestOutcome outcome
     * @param   string inc
     * @return  xml.Node
     */
    protected function addTestCase(TestOutcome $outcome, $inc= NULL) {
      $testClass= $outcome->test->getClass();

      // Update test suite
      // Update test count
      $n= $this->testNode($outcome->test);
      $n->setAttribute('tests', $n->getAttribute('tests')+ 1);
      $n->setAttribute('assertions', $n->getAttribute('assertions')+ $outcome->test->getAssertions());
      $inc && $n->setAttribute($inc, $n->getAttribute($inc)+ 1);

      //Update wrappernode
      $this->wrapperNode->setAttribute('tests', $this->wrapperNode->getAttribute('tests')+ 1);
      $this->wrapperNode->setAttribute(
        'assertions',
        $this->wrapperNode->getAttribute('assertions')+ $outcome->test->getAssertions());
      $inc && $this->wrapperNode->setAttribute($inc, $this->wrapperNode->getAttribute($inc)+ 1);
      $this->overalltime+= $outcome->elapsed;
      $this->wrapperNode->setAttribute('time', sprintf('%.6f', $this->overalltime));
      
      // Add testcase information
      return $n->addChild(new Node('testcase', NULL, array(
        'name'       => $outcome->test->getName(),
        'class'      => $testClass->getName(),
        'file'       => $this->getFileUri($testClass),
        'line'       => $this->getStartLine($testClass, $outcome->test->getName()),
        'assertions' => $outcome->test->getAssertions(),
        'time'       => sprintf('%.6f', $outcome->elapsed)
      )));
    }

    /**
     * Called when a test fails.
     *
     * @param   unittest.TestFailure failure
     */
    public function testFailed(TestFailure $failure) {
      $testClass= $failure->test->getClass();
      $trace= $failure->reason->getStackTrace();

      $content= sprintf(
         "%s(%s)\n%s \n\n%s:%d\n\n",
         $testClass->getName(),
          $failure->test->getName(),
          trim($failure->reason->compoundMessage()),
          $this->getFileUri($testClass),
          $this->getStartLine($testClass, $failure->test->getName())
      );

      $t= $this->addTestCase($failure, 'failures');
      $t->addChild(new Node('failure', $content, array(
        'message' => trim($failure->reason->compoundMessage()),
        'type'    => xp::typeOf($failure->reason)
      )));
    }

    /**
     * Called when a test errors.
     *
     * @param   unittest.TestError error
     */
    public function testError(TestError $error) {
      $testClass= $error->test->getClass();
      $trace= $error->reason->getStackTrace();

      $content= sprintf(
         "%s(%s)\n%s \n\n%s:%d\n\n",
         $testClass->getName(),
          $error->test->getName(),
          trim($error->reason->compoundMessage()),
          $this->getFileUri($testClass),
          $this->getStartLine($testClass, $error->test->getName())
      );

      $t= $this->addTestCase($error, 'failures');
      $t->addChild(new Node('error', $content, array(
        'message' => trim($error->reason->compoundMessage()),
        'type'    => xp::typeOf($error->reason)
      )));
    }

    /**
     * Called when a test raises warnings.
     *
     * @param   unittest.TestWarning warning
     */
    public function testWarning(TestWarning $warning) {
      $t= $this->addTestCase($warning, 'errors');
      $t->addChild(new Node('error', implode("\n", $warning->reason), array(
        'message' => 'Non-clear error stack',
        'type'    => 'warnings'
      )));
    }
    
    /**
     * Called when a test finished successfully.
     *
     * @param   unittest.TestSuccess success
     */
    public function testSucceeded(TestSuccess $success) {
      $this->addTestCase($success);
    }
    
    /**
     * Called when a test is not run because it is skipped due to a 
     * failed prerequisite.
     *
     * @param   unittest.TestSkipped skipped
     */
    public function testSkipped(TestSkipped $skipped) {
      if ($skipped->reason instanceof Throwable) {
        $reason= trim($skipped->reason->compoundMessage());
      } else {
        $reason= $skipped->reason;
      }
      $this->addTestCase($skipped, 'skipped')->setAttribute('skipped', $reason);
    }

    /**
     * Called when a test is not run because it has been ignored by using
     * the @ignore annotation.
     *
     * @param   unittest.TestSkipped ignore
     */
    public function testNotRun(TestSkipped $ignore) {
      $this->addTestCase($ignore, 'skipped')->setAttribute('skipped', $ignore->reason);
    }

    /**
     * Called when a test run starts.
     *
     * @param   unittest.TestSuite suite
     */
    public function testRunStarted(TestSuite $suite) {
      // NOOP
    }
    
    /**
     * Called when a test run finishes.
     *
     * @param   unittest.TestSuite suite
     * @param   unittest.TestResult result
     */
    public function testRunFinished(TestSuite $suite, TestResult $result) {
      $this->out->write($this->tree->getDeclaration()."\n");
      $this->out->write($this->tree->getSource(INDENT_DEFAULT));
    }
  }
?>
