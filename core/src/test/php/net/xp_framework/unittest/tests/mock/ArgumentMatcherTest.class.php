<?php

/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.mock.arguments.IArgumentMatcher',
    'unittest.mock.arguments.AnyMatcher',
    'unittest.mock.arguments.DynamicMatcher',
    'util.Date'
  );

  /**
   * Test cases for argument matchers
   */
  class ArgumentMatcherTest extends TestCase {
    /**
     * AnyMatcher should return true.
     */
    #[@test]
    public function AnyMatchter_should_work() {
      $matcher = Arg::any();
      $this->assertTrue($matcher->matches(null));
      $this->assertTrue($matcher->matches(1));
      $this->assertTrue($matcher->matches(""));
      $this->assertTrue($matcher->matches(new Object()));
      
      
    }
    
        /**
     * AnyMatcher should return true.
     */
    #[@test]
    public function DynamicMatchter_should_work() {
      $matcher= Arg::func("matchEmpty", "ArgumentMatcherTest");
      $this->assertFalse($matcher->matches(null));
      $this->assertFalse($matcher->matches(1));
      $this->assertTrue($matcher->matches(""));
      $this->assertFalse($matcher->matches(new Object()));
    }
    public static function matchEmpty($string) {
      return $string === "";
    }
    
    /**
     * AnyMatcher should return true.
     */
    #[@test]
    public function TypeMatcher_should_work() {
      $matcher = Arg::anyOfType('util.Date');
      $this->assertTrue($matcher->matches(null));
      $this->assertFalse($matcher->matches(1));
      $this->assertFalse($matcher->matches(""));
      $this->assertTrue($matcher->matches(Date::now()));
    }


  }
?>