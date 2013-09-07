<?php namespace net\xp_framework\unittest\core\generics;

/**
 * TestCase for generic behaviour at runtime.
 *
 * @see   xp://net.xp_framework.unittest.core.generics.ListOf
 */
class GenericsOfGenericsTest extends \unittest\TestCase {
  
  #[@test]
  public function listOfListOfStringsReflection() {
    $l= create('new net.xp_framework.unittest.core.generics.ListOf<net.xp_framework.unittest.core.generics.ListOf<string>>');
    
    with ($class= $l->getClass()); {
      $this->assertTrue($class->isGeneric());
      $arguments= $class->genericArguments();
      $this->assertEquals(1, sizeof($arguments));
      
      with ($cclass= $arguments[0]); {
        $this->assertTrue($cclass->isGeneric());
        $arguments= $cclass->genericArguments();
        $this->assertEquals(1, sizeof($arguments));
        $this->assertEquals(\lang\Primitive::$STRING, $arguments[0]);
      }
    }
  }

  #[@test]
  public function lookupOfListOfStringsReflection() {
    $l= create('new net.xp_framework.unittest.core.generics.Lookup<string, net.xp_framework.unittest.core.generics.ListOf<string>>');
    
    with ($class= $l->getClass()); {
      $this->assertTrue($class->isGeneric());
      $arguments= $class->genericArguments();
      $this->assertEquals(2, sizeof($arguments));
      
      $this->assertEquals(\lang\Primitive::$STRING, $arguments[0]);
      with ($vclass= $arguments[1]); {
        $this->assertTrue($vclass->isGeneric());
        $arguments= $vclass->genericArguments();
        $this->assertEquals(1, sizeof($arguments));
        $this->assertEquals(\lang\Primitive::$STRING, $arguments[0]);
      }
    }
  }

  #[@test]
  public function lookupOfLookupOfStringsReflection() {
    $l= create('new net.xp_framework.unittest.core.generics.Lookup<string, net.xp_framework.unittest.core.generics.Lookup<string, lang.Generic>>');
    
    with ($class= $l->getClass()); {
      $this->assertTrue($class->isGeneric());
      $arguments= $class->genericArguments();
      $this->assertEquals(2, sizeof($arguments));
      
      $this->assertEquals(\lang\Primitive::$STRING, $arguments[0]);
      with ($vclass= $arguments[1]); {
        $this->assertTrue($vclass->isGeneric());
        $arguments= $vclass->genericArguments();
        $this->assertEquals(2, sizeof($arguments));
        $this->assertEquals(\lang\Primitive::$STRING, $arguments[0]);
        $this->assertEquals(\lang\XPClass::forName('lang.Generic'), $arguments[1]);
      }
    }
  }
}
