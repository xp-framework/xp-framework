<?php namespace net\xp_framework\unittest\tests;
 
use unittest\TestCase;


/**
 * This class is used in the SuiteTest 
 *
 * @purpose  Unit Test
 */
class AnotherTestCase extends TestCase {

  /**
   * Always succeeds
   *
   */
  #[@test]
  public function succeeds() {
    $this->assertTrue(true);
  }
}
