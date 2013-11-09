<?php namespace net\xp_framework\unittest\tests;

/**
 * TestCase for which setUp() method fails.
 */
class SetUpFailingTestCase extends \unittest\TestCase {

  /**
   * Sets up test case - throw an exception not derived from
   * unittest.PrerequisitesNotMetError or unittest.AssertionFailedError
   * which are expected.
   *
   */
  public function setUp() {
    throw new \lang\IllegalArgumentException('Something went wrong in setup.');
  }

  #[@test]
  public function emptyTest() {
  }
}
