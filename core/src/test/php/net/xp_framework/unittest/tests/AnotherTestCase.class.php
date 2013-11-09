<?php namespace net\xp_framework\unittest\tests;
 
/**
 * This class is used in the SuiteTest 
 */
class AnotherTestCase extends \unittest\TestCase {

  /**
   * Always succeeds
   */
  #[@test]
  public function succeeds() {
    $this->assertTrue(true);
  }
}
