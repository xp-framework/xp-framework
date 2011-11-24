<?php

/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.mock.arguments.IArgumentMatcher',
    'unittest.mock.arguments.AnyMatcher',
    'unittest.mock.arguments.DynamicMatcher'
  );

  /**
   * Test cases for argument matchers
   */
  class ArgumentMatcherTest extends TestCase {
    /**
     * AnyMatcher should return true.
     */
    #[@test]
    public function AnyMatchter_should_return_true() {
      $matcher = new AnyMatcher();
      $this->assertTrue($matcher->matches(null));
      $this->assertTrue($matcher->matches(1));
      $this->assertTrue($matcher->matches(""));
      $this->assertTrue($matcher->matches(new Object()));
    }
    
        /**
     * AnyMatcher should return true.
     */
    #[@test]
    public function DynamicMatchter_should_return_true() {
      $matcher = new DynamicMatcher("matchEmpty", "ArgumentMatcherTest");
      $this->assertFalse($matcher->matches(null));
      $this->assertFalse($matcher->matches(1));
      $this->assertTrue($matcher->matches(""));
      $this->assertFalse($matcher->matches(new Object()));
    }
    
    public static function matchEmpty($string) {
      return $string === "";
    }

  }
?>