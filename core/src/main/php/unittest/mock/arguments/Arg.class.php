<?php

/* This class is part of the XP framework
 *
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

    /*
     * Accessor method for the any matcher.
     */
    public static function any() {
      return self::$any;
    }
    
    public static function func($func, $classOrObj= NULL) {
      return new DynamicMatcher($func, $classOrObj);
    }
    
  }
?>