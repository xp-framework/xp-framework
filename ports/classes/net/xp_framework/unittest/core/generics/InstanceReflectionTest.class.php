<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'net.xp_framework.unittest.core.generics.Lookup',
    'lang.types.String'
  );

  /**
   * TestCase for instance reflection
   *
   * @see   xp://net.xp_framework.unittest.core.generics.Lookup
   */
  class InstanceReflectionTest extends TestCase {
    protected $fixture= NULL;
    
    /**
     * Creates fixture, a Lookup with String and TestCase as component
     * types.
     *
     */  
    public function setUp() {
      $this->fixture= create('new net.xp_framework.unittest.core.generics.Lookup<String, TestCase>()');
    }
  
    /**
     * Test getClassName() on generic instance
     *
     */
    #[@test]
    public function getClassNameMethod() {
      $this->assertEquals(
        'net.xp_framework.unittest.core.generics.Lookup`2[lang.types.String,unittest.TestCase]', 
        $this->fixture->getClassName()
      );
    }

    /**
     * Test getClass()
     *
     */
    #[@test]
    public function nameOfClass() {
      $class= $this->fixture->getClass();
      $this->assertEquals(
        'net.xp_framework.unittest.core.generics.Lookup`2[lang.types.String,unittest.TestCase]', 
        $class->getName()
      );
    }

    /**
     * Test getClass()
     *
     */
    #[@test]
    public function simpleNameOfClass() {
      $class= $this->fixture->getClass();
      $this->assertEquals(
        'Lookup`2[lang.types.String,unittest.TestCase]', 
        $class->getSimpleName()
      );
    }

    /**
     * Test reflected name
     *
     */
    #[@test]
    public function reflectedNameOfClass() {
      $class= $this->fixture->getClass();
      $this->assertEquals(
        'net·xp_framework·unittest·core·generics·Lookup··String¸TestCase', 
        xp::reflect($class->getName())
      );
    }

    /**
     * Test isGeneric()
     *
     */
    #[@test]
    public function instanceIsGeneric() {
      $this->assertTrue($this->fixture->getClass()->isGeneric());
    }

    /**
     * Test isGenericDefinition()
     *
     */
    #[@test]
    public function instanceIsNoGenericDefinition() {
      $this->assertFalse($this->fixture->getClass()->isGenericDefinition());
    }

    /**
     * Test genericDefinition()
     *
     */
    #[@test]
    public function genericDefinition() {
      $this->assertEquals(
        XPClass::forName('net.xp_framework.unittest.core.generics.Lookup'),
        $this->fixture->getClass()->genericDefinition()
      );
    }

    /**
     * Test isGenericDefinition()
     *
     */
    #[@test]
    public function genericArguments() {
      $this->assertEquals(
        array(XPClass::forName('lang.types.String'), XPClass::forName('unittest.TestCase')),
        $this->fixture->getClass()->genericArguments()
      );
    }

    /**
     * Test parameter reflection
     *
     */
    #[@test, @ignore('No longer existant in new implementation')]
    public function delegateFieldType() {
      $this->assertEquals(
        'net.xp_framework.unittest.core.generics.Lookup',
        $this->fixture->getClass()->getField('delegate')->getType()
      );
    }

    /**
     * Test parameter reflection
     *
     */
    #[@test]
    public function putParameters() {
      $params= $this->fixture->getClass()->getMethod('put')->getParameters();
      $this->assertEquals(2, sizeof($params));
      $this->assertEquals(XPClass::forName('lang.types.String'), $params[0]->getType());
      $this->assertEquals(XPClass::forName('unittest.TestCase'), $params[1]->getType());
    }

    /**
     * Test return type reflection
     *
     */
    #[@test]
    public function getReturnType() {
      $this->assertEquals(
        'unittest.TestCase',
        $this->fixture->getClass()->getMethod('get')->getReturnTypeName()
      );
    }

    /**
     * Test return type reflection
     *
     */
    #[@test]
    public function valuesReturnType() {
      $this->assertEquals(
        'unittest.TestCase[]',
        $this->fixture->getClass()->getMethod('values')->getReturnTypeName()
      );
    }
  }
?>
