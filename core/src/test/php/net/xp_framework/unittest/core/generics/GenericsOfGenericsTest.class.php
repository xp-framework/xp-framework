<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_framework.unittest.core.generics';

  uses(
    'unittest.TestCase',
    'net.xp_framework.unittest.core.generics.ListOf',
    'net.xp_framework.unittest.core.generics.Lookup'
  );

  /**
   * TestCase for generic behaviour at runtime.
   *
   * @see   xp://net.xp_framework.unittest.core.generics.ListOf
   */
  class net·xp_framework·unittest·core·generics·GenericsOfGenericsTest extends TestCase {
    
    /**
     * Test a list of list of strings
     *
     */
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
          $this->assertEquals(Primitive::$STRING, $arguments[0]);
        }
      }
    }

    /**
     * Test a lookup of list of strings
     *
     */
    #[@test]
    public function lookupOfListOfStringsReflection() {
      $l= create('new net.xp_framework.unittest.core.generics.Lookup<string, net.xp_framework.unittest.core.generics.ListOf<string>>');
      
      with ($class= $l->getClass()); {
        $this->assertTrue($class->isGeneric());
        $arguments= $class->genericArguments();
        $this->assertEquals(2, sizeof($arguments));
        
        $this->assertEquals(Primitive::$STRING, $arguments[0]);
        with ($vclass= $arguments[1]); {
          $this->assertTrue($vclass->isGeneric());
          $arguments= $vclass->genericArguments();
          $this->assertEquals(1, sizeof($arguments));
          $this->assertEquals(Primitive::$STRING, $arguments[0]);
        }
      }
    }

    /**
     * Test a lookup of lookup of strings
     *
     */
    #[@test]
    public function lookupOfLookupOfStringsReflection() {
      $l= create('new net.xp_framework.unittest.core.generics.Lookup<string, net.xp_framework.unittest.core.generics.Lookup<string, lang.Generic>>');
      
      with ($class= $l->getClass()); {
        $this->assertTrue($class->isGeneric());
        $arguments= $class->genericArguments();
        $this->assertEquals(2, sizeof($arguments));
        
        $this->assertEquals(Primitive::$STRING, $arguments[0]);
        with ($vclass= $arguments[1]); {
          $this->assertTrue($vclass->isGeneric());
          $arguments= $vclass->genericArguments();
          $this->assertEquals(2, sizeof($arguments));
          $this->assertEquals(Primitive::$STRING, $arguments[0]);
          $this->assertEquals(XPClass::forName('lang.Generic'), $arguments[1]);
        }
      }
    }
  }
?>
