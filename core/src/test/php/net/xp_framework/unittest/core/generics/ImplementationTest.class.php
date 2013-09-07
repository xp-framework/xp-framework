<?php namespace net\xp_framework\unittest\core\generics;

use unittest\TestCase;


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
      array(\lang\Primitive::$STRING), 
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
      \lang\XPClass::forName('lang.Type'),
      $fixture->getClass()->getMethod('put')->getParameter(0)->getType()
    );
  }

  /**
   * Test generic arguments
   *
   */
  #[@test, @ignore('Needs implementation change to copy all methods')]
    public function abstractTypeDictionaryPutMethodKeyParameter() {
    $fixture= \lang\Type::forName('net.xp_framework.unittest.core.generics.AbstractTypeDictionary<string>');
    $this->assertEquals(
      \lang\XPClass::forName('lang.Type'),
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
    $fixture->put(\lang\Primitive::$STRING, 'string');
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
      array(\lang\XPClass::forName('lang.Type'), \lang\Primitive::$STRING), 
      this($fixture->getClass()->getInterfaces(), 0)->genericArguments()
    );
  }

  /**
   * Test generic components
   *
   */
  #[@test]
    public function typeDictionaryClass() {
    $fixture= \lang\Type::forName('net.xp_framework.unittest.core.generics.TypeDictionary');
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
    $fixture= \lang\Type::forName('net.xp_framework.unittest.core.generics.AbstractTypeDictionary');
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
    $fixture= \lang\Type::forName('net.xp_framework.unittest.core.generics.AbstractTypeDictionary');
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
    $fixture= \lang\Type::forName('net.xp_framework.unittest.core.generics.AbstractTypeDictionary<string>');
    $this->assertEquals(
      array(\lang\XPClass::forName('lang.Type'), \lang\Primitive::$STRING), 
      this($fixture->getInterfaces(), 1)->genericArguments()
    );
  }

  /**
   * Test closed generic
   */
  #[@test]
    public function closed() {
    $this->assertEquals(
      \lang\XPClass::forName('lang.Object'),
      \lang\Type::forName('net.xp_framework.unittest.core.generics.ListOf<string>')->getParentclass()
    );
  }

  /**
   * Test closed generic
   */
  #[@test]
    public function closedNS() {
    $this->assertEquals(
      \lang\XPClass::forName('lang.Object'),
      \lang\Type::forName('net.xp_framework.unittest.core.generics.NSListOf<string>')->getParentclass()
    );
  }

  /**
   * Test partially closed generic
   */
  #[@test]
    public function partiallyClosed() {
    $this->assertEquals(
      \lang\Type::forName('net.xp_framework.unittest.core.generics.Lookup<lang.Type, string>'),
      \lang\Type::forName('net.xp_framework.unittest.core.generics.TypeLookup<string>')->getParentclass()
    );
  }

  /**
   * Test partially closed generic
   */
  #[@test]
    public function partiallyClosedNS() {
    $this->assertEquals(
      \lang\Type::forName('net.xp_framework.unittest.core.generics.NSLookup<lang.Type, string>'),
      \lang\Type::forName('net.xp_framework.unittest.core.generics.NSTypeLookup<string>')->getParentclass()
    );
  }
}
