<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.mock.arguments.IArgumentMatcher',
    'unittest.mock.arguments.AnyMatcher'
  );


/**
 * Convenience class providing common argument matchers.
 *
 * @purpose Argument matching.
 */
  class Arg extends Object {
    private static $any;

    /**
     * Static constructor. Sets up the matchers
     */
    static function __static() {
      self::$any= new AnyMatcher();
    }

    public static function any() {
      return self::$any;
    }
    
  }
?>