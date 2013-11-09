<?php namespace net\xp_framework\unittest\tests;

/**
 * Tests @beforeClass and @afterClass methods using run()
 *
 * @see   xp://unittest.TestSuite
 */
class BeforeAndAfterClassWithRunTest extends BeforeAndAfterClassTest {

  /**
   * Runs a test and returns the outcome
   *
   * @param   unittest.TestCase $test
   * @return  unittest.TestOutcome
   */
  protected function runTest($test) {
    $this->suite->addTest($test);
    return $this->suite->run();
  }
}
