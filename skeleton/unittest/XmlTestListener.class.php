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
          'line'       => '1',
          'file'       => $class->getSimpleName().xp::CLASS_FILE_EXT,
          'tests'      => 0,
          'failures'   => 0,
          'errors'     => 0,
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
    protected function addTestCase(TestOutcome $outcome, $inc) {
      $testClass= $outcome->test->getClass();
      
      // Update test count
      $n= $this->classes[$testClass];
      $n->setAttribute('tests', $n->getAttribute('tests')+ 1);
      $n->setAttribute($inc, $n->getAttribute($inc)+ 1);
      
      // Add testcase information
      return $n->addChild(new Node('testcase', NULL, array(
        'name'       => $outcome->test->getName(),
        'class'      => $testClass->getName(),
        'time'       => sprintf('%.6f', $outcome->elapsed),
        'assertions' => '1',
        'line'       => '-1',    // FIXME
        'file'       => $testClass->getSimpleName().xp::CLASS_FILE_EXT,
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
        'type'    => xp::typeOf($failure->reason)
      )));
    }
    
    /**
     * Called when a test finished successfully.
     *
     * @param   unittest.TestSuccess success
     */
    public function testSucceeded(TestSuccess $success) {
      $this->addTestCase($success, 'assertions');
    }
    
    /**
     * Called when a test is not run - usually because it is skipped
     * due to a non-met prerequisite or if it has been ignored by using
     * the @ignore annotation.
     *
     * @param   unittest.TestSkipped skipped
     */
    public function testSkipped(TestSkipped $skipped) {
      // Not supported?
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
