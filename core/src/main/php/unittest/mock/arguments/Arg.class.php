<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.mock.arguments.IArgumentMatcher',
    'unittest.mock.arguments.AnyMatcher',
    'unittest.mock.arguments.DynamicMatcher',
    'unittest.mock.arguments.TypeMatcher',
    'unittest.mock.MockProxyBuilder'
  );

  /**
   * Convenience class providing common argument matchers.
   *
   * @test  xp://net.xp_framework.unittest.tests.mock.ArgumentMatcherTest
   */
  class Arg extends Object {
    private static $any;
    
    static function __static() {
      self::$any= new AnyMatcher();
    }

    /**
     * Accessor method for the any matcher.
     *
     */
    public static function any() {
      return self::$any;
    }
    
    /**
     * Accessor method for a dynamic matcher with a specified function.
     * 
     * @param   string func
     * @param   var classOrObject
     */
    public static function func($func, $classOrObj= NULL) {
      return new DynamicMatcher($func, $classOrObj);
    }
    
    /**
     * Accessor method for a type matcher.
     * 
     * @param   typeName string
     */
    public static function anyOfType($typeName) {
      $builder= new MockProxyBuilder();
      $builder->setOverwriteExisting(FALSE);
      
      $interfaces= array(XPClass::forName('unittest.mock.arguments.IArgumentMatcher'));
      $parentClass= NULL;
      
      $type= XPClass::forName($typeName);
      if ($type->isInterface()) {
        $interfaces[]= $type;
      } else {
        $parentClass= $type;
      }
      
      $proxyClass= $builder->createProxyClass(ClassLoader::getDefault(), $interfaces, $parentClass);
      return $proxyClass->newInstance(new TypeMatcher($typeName));
    }


    /**
     * Accessor method for a pattern matcher.
     * 
     * @param   pattern string
     */
    public static function match($pattern) {
      return new PatternMatcher($pattern);
    }
  }
?>
