<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.mock.arguments.Arg',
    'util.Date'
  );

  /**
   * Testcase for the Arg convenience class
   *
   * @see   xp://unittest.mock.arguments.Arg
   */
  class ArgumentMatcherTest extends TestCase {

    /**
     * Arg::any()
     *
     */
    #[@test]
    public function any_should_match_integers() {
      $this->assertTrue(Arg::any()->matches(1));
    }

    /**
     * Arg::any()
     *
     */
    #[@test]
    public function any_should_match_strings() {
      $this->assertTrue(Arg::any()->matches(''));
    }

    /**
     * Arg::any()
     *
     */
    #[@test]
    public function any_should_match_an_object() {
      $this->assertTrue(Arg::any()->matches(new Object));
    }

    /**
     * Arg::any()
     *
     */
    #[@test]
    public function any_should_match_null() {
      $this->assertTrue(Arg::any()->matches(NULL));
    }

    /**
     * Callback for Arg::func()
     *
     * @param   string
     * @return  bool
     */
    public static function matchEmpty($string) {
      return '' === $string;
    }
    
    /**
     * Arg::func()
     *
     */
    #[@test]
    public function dynamic_with_this_matchEmpty_should_match_empty_string() {
      $this->assertTrue(Arg::func('matchEmpty', $this)->matches(''));
    }

    /**
     * Arg::func()
     *
     */
    #[@test]
    public function dynamic_with_static_matchEmpty_should_match_empty_string() {
      $this->assertTrue(Arg::func('matchEmpty', __CLASS__)->matches(''));
    }

    /**
     * Arg::func()
     *
     */
    #[@test]
    public function dynamic_with_matchEmpty_should_not_match_null() {
      $this->assertFalse(Arg::func('matchEmpty', $this)->matches(NULL));
    }

    /**
     * Arg::func()
     *
     */
    #[@test]
    public function dynamic_with_matchEmpty_should_not_match_objects() {
      $this->assertFalse(Arg::func('matchEmpty', $this)->matches(new Object()));
    }

    /**
     * Arg::anyOfType()
     *
     */
    #[@test]
    public function typeof_date_should_match_date_instance() {
      $this->assertTrue(Arg::anyOfType('util.Date')->matches(Date::now()));
    }

    /**
     * Arg::anyOfType()
     *
     */
    #[@test]
    public function typeof_date_should_match_null() {
      $this->assertTrue(Arg::anyOfType('util.Date')->matches(NULL));
    }

    /**
     * Arg::anyOfType()
     *
     */
    #[@test]
    public function typeof_date_should_not_match_objects() {
      $this->assertFalse(Arg::anyOfType('util.Date')->matches(new Object()));
    }

    /**
     * Arg::anyOfType()
     *
     */
    #[@test]
    public function typeof_date_should_not_match_primitives() {
      $this->assertFalse(Arg::anyOfType('util.Date')->matches(1));
    }
  }
?>
