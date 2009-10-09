<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestListener', 
    'io.streams.OutputStreamWriter', 
    'xml.Tree',
    'util.collections.HashTable'
  );

  /**
   * Creates an XML file suitable for importing into continuous integration
   * systems like Hudson.
   *
   * @test     xp://net.xp_framework.unittests.tests.XmlListenerTest
   * @purpose  TestListener
   */
  class XmlTestListener extends Object implements TestListener {
    public $out= NULL;
    protected $classes= NULL;
    
    /**
     * Constructor
     *
     * @param   io.streams.OutputStreamWriter out
     */
    public function __construct(OutputStreamWriter $out) {
      $this->out= $out;
      $this->tree= new Tree('testsuites');
      $this->classes= create('new util.collections.HashTable<lang.XPClass, xml.Node>()');
    }
    
    /**
     * Called when a test case starts.
     *
     * @param   unittest.TestCase failure
     */
    public function testStarted(TestCase $case) {
      $class= $case->getClass();
      if (!$this->classes->containsKey($class)) {
        $this->classes[$class]= $this->tree->addChild(new Node('testsuite', NULL, array(
          'name'       => $class->getName(),
          'tests'      => 0,
          'failures'   => 0,
          'errors'     => 0,
          'skipped'    => 0
        )));
      }
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
      
      // Update test count
      $n= $this->classes[$testClass];
      $n->setAttribute('tests', $n->getAttribute('tests')+ 1);
      $inc && $n->setAttribute($inc, $n->getAttribute($inc)+ 1);
      
      // Add testcase information
      return $n->addChild(new Node('testcase', NULL, array(
        'name'       => $outcome->test->getName(),
        'class'      => $testClass->getName(),
        'time'       => sprintf('%.6f', $outcome->elapsed)
      )));
    }

    /**
     * Called when a test fails.
     *
     * @param   unittest.TestFailure failure
     */
    public function testFailed(TestFailure $failure) {
      $t= $this->addTestCase($failure, 'failures');
      $t->addChild(new Node('failure', xp::stringOf($failure->reason), array(
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
      $t= $this->addTestCase($error, 'errors');
      $t->addChild(new Node('error', xp::stringOf($error->reason), array(
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
