<?php namespace net\xp_framework\unittest\tests;

/**
 * Tests @beforeClass and @afterClass methods using runTest()
 *
 * @see   xp://unittest.TestSuite
 */
class BeforeAndAfterClassWithRunTestTest extends BeforeAndAfterClassTest {

  /**
   * Runs a test and returns the outcome
   *
   * @param   unittest.TestCase $test
   * @return  unittest.TestOutcome
   */
  protected function runTest($test) {
    return $this->suite->runTest($test);
  }
}
