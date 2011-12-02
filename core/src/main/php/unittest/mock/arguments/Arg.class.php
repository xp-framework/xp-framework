<?php

/* This class is part of the XP framework
 *
 */

  uses(
    'unittest.mock.arguments.IArgumentMatcher',
    'unittest.mock.arguments.AnyMatcher',
    'unittest.mock.arguments.TypeMatcher',
    'unittest.mock.MockProxyBuilder'
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
    
    /*
     * Accessor method for a dynamic matcher with a specified function.
     * 
     * @param func string
     * @param classOrObject mixed
     */
    public static function func($func, $classOrObj= NULL) {
      return new DynamicMatcher($func, $classOrObj);
    }
    
    /*
     * Accessor method for a type matcher.
     * 
     * @param typeName string
     */
    public static function anyOfType($typeName) {
      $builder= new MockProxyBuilder();
      $builder->setOverwriteExisting(false);
      
      $defaultCL= ClassLoader::getDefault();
      
      $interfaces= array();
      $parentClass= NULL;
      
      $type= XPClass::forName($typeName);
      if($type->isInterface()) 
        $interfaces[]= $type;
      else
        $parentClass= $type;
        
      $proxyClass= $builder->createProxyClass($defaultCL, $interfaces, $parentClass);
      return $proxyClass->newInstance(new TypeMatcher($typeName));
    }
  }
?>