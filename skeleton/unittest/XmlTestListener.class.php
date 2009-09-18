<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestListener', 'io.streams.OutputStreamWriter', 'xml.Tree');

  /**
   * Creates an XML file
   *
   * @purpose  TestListener
   */
  class XmlTestListener extends Object implements TestListener {
    public
      $out= NULL;
    
    /**
     * Constructor
     *
     * @param   io.streams.OutputStreamWriter out
     */
    public function __construct(OutputStreamWriter $out) {
      $this->out= $out;
      $this->tree= new Tree('testsuites');
    }
    
    /**
     * Called when a test case starts.
     *
     * @param   unittest.TestCase failure
     */
    public function testStarted(TestCase $case) {
      // NOOP
    }
    
    protected function addTestCase(TestOutcome $outcome) {
      return $this->suite->addChild(new Node('testcase', NULL, array(
        'name'       => $outcome->test->getName(),
        'class'      => $outcome->test->getClassName(),
        'time'       => sprintf('%.6f', $outcome->elapsed),
        'assertions' => '1',
        'line'       => '-1',    // FIXME
        'file'       => '',      // FIXME
      )));
    }

    /**
     * Called when a test fails.
     *
     * @param   unittest.TestFailure failure
     */
    public function testFailed(TestFailure $failure) {
      $t= $this->addTestCase($failure);
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
      $this->addTestCase($success);
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
      $this->suite= $this->tree->addChild(new Node('testsuite', NULL, array(
        'name'       => 'alltests',  // FIXME
        'line'       => '-1',    // FIXME
        'file'       => '',      // FIXME
        'tests'      => $suite->numTests(),
      )));
    }
    
    /**
     * Called when a test run finishes.
     *
     * @param   unittest.TestSuite suite
     * @param   unittest.TestResult result
     */
    public function testRunFinished(TestSuite $suite, TestResult $result) {
      $this->suite->setAttribute('failures', $result->failureCount());
      $this->suite->setAttribute('assertions', $result->successCount());
      $this->suite->setAttribute('errors', '0');
      
      $this->out->write($this->tree->getDeclaration()."\n");
      $this->out->write($this->tree->getSource(INDENT_DEFAULT));
    }
  }
?>
