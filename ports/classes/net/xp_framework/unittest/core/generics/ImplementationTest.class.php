<?php
/* This class is part of the XP framework
 *
 * $Id: InstanceReflectionTest.class.php 14412 2010-03-29 17:35:16Z friebe $ 
 */

  uses(
    'unittest.TestCase',
    'net.xp_framework.unittest.core.generics.TypeDictionary',
    'net.xp_framework.unittest.core.generics.TypeLookup'
  );

  /**
   * TestCase for instance reflection
   *
   * @see   xp://net.xp_framework.unittest.core.generics.TypeDictionary
   * @see   xp://net.xp_framework.unittest.core.generics.TypeLookup
   */
  class ImplementationTest extends TestCase {
  
    /**
     * Test generic arguments
     *
     */
    #[@test]
    public function typeDictionaryInstance() {
      $fixture= create('new net.xp_framework.unittest.core.generics.TypeDictionary<string>');
      $this->assertEquals(
        array(Primitive::$STRING), 
        $fixture->getClass()->genericArguments()
      );
    }

    /**
     * Test generic arguments
     *
     */
    #[@test]
    public function typeDictionaryPutMethodKeyParameter() {
      $fixture= create('new net.xp_framework.unittest.core.generics.TypeDictionary<string>');
      $this->assertEquals(
        XPClass::forName('lang.Type'),
        $fixture->getClass()->getMethod('put')->getParameter(0)->getType()
      );
    }

    /**
     * Test generic arguments
     *
     */
    #[@test, @ignore('Needs implementation change to copy all methods')]
    public function abstractTypeDictionaryPutMethodKeyParameter() {
      $fixture= Type::forName('net.xp_framework.unittest.core.generics.AbstractTypeDictionary<string>');
      $this->assertEquals(
        XPClass::forName('lang.Type'),
        $fixture->getMethod('put')->getParameter(0)->getType()
      );
    }

    /**
     * Test put()
     *
     */
    #[@test]
    public function put() {
      $fixture= create('new net.xp_framework.unittest.core.generics.TypeDictionary<string>');
      $fixture->put(Primitive::$STRING, 'string');
    }

    /**
     * Test put()
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function putInvalid() {
      $fixture= create('new net.xp_framework.unittest.core.generics.TypeDictionary<string>');
      $fixture->put($this, 'string');
    }

    /**
     * Test generic arguments
     *
     */
    #[@test]
    public function typeDictionaryInstanceInterface() {
      $fixture= create('new net.xp_framework.unittest.core.generics.TypeDictionary<string>');
      $this->assertEquals(
        array(XPClass::forName('lang.Type'), Primitive::$STRING), 
        this($fixture->getClass()->getInterfaces(), 0)->genericArguments()
      );
    }

    /**
     * Test generic components
     *
     */
    #[@test]
    public function typeDictionaryClass() {
      $fixture= Type::forName('net.xp_framework.unittest.core.generics.TypeDictionary');
      $this->assertEquals(
        array('V'), 
        $fixture->genericComponents()
      );
    }

    /**
     * Test generic components
     *
     */
    #[@test]
    public function abstractTypeDictionaryClass() {
      $fixture= Type::forName('net.xp_framework.unittest.core.generics.AbstractTypeDictionary');
      $this->assertEquals(
        array('V'), 
        $fixture->genericComponents()
      );
    }

    /**
     * Test generic components
     *
     */
    #[@test]
    public function dictionaryInterfaceDefinition() {
      $fixture= Type::forName('net.xp_framework.unittest.core.generics.AbstractTypeDictionary');
      $this->assertEquals(
        array('K', 'V'), 
        this($fixture->getInterfaces(), 1)->genericComponents()
      );
    }

    /**
     * Test generic arguments
     *
     */
    #[@test]
    public function dictionaryInterface() {
      $fixture= Type::forName('net.xp_framework.unittest.core.generics.AbstractTypeDictionary<string>');
      $this->assertEquals(
        array(XPClass::forName('lang.Type'), Primitive::$STRING), 
        this($fixture->getInterfaces(), 1)->genericArguments()
      );
    }
  }
?>
