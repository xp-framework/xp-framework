<?php namespace net\xp_framework\unittest\core\generics;

use lang\Type;
use lang\Primitive;
use lang\XPClass;

/**
 * TestCase for instance reflection
 *
 * @see   xp://net.xp_framework.unittest.core.generics.TypeDictionary
 * @see   xp://net.xp_framework.unittest.core.generics.TypeLookup
 */
class ImplementationTest extends \unittest\TestCase {

  #[@test]
  public function typeDictionaryInstance() {
    $fixture= create('new net.xp_framework.unittest.core.generics.TypeDictionary<string>');
    $this->assertEquals(
      array(Primitive::$STRING), 
      $fixture->getClass()->genericArguments()
    );
  }

  #[@test]
  public function typeDictionaryPutMethodKeyParameter() {
    $fixture= create('new net.xp_framework.unittest.core.generics.TypeDictionary<string>');
    $this->assertEquals(
      XPClass::forName('lang.Type'),
      $fixture->getClass()->getMethod('put')->getParameter(0)->getType()
    );
  }

  #[@test, @ignore('Needs implementation change to copy all methods')]
  public function abstractTypeDictionaryPutMethodKeyParameter() {
    $fixture= Type::forName('net.xp_framework.unittest.core.generics.AbstractTypeDictionary<string>');
    $this->assertEquals(
      XPClass::forName('lang.Type'),
      $fixture->getMethod('put')->getParameter(0)->getType()
    );
  }

  #[@test]
  public function put() {
    $fixture= create('new net.xp_framework.unittest.core.generics.TypeDictionary<string>');
    $fixture->put(Primitive::$STRING, 'string');
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function putInvalid() {
    $fixture= create('new net.xp_framework.unittest.core.generics.TypeDictionary<string>');
    $fixture->put($this, 'string');
  }

  #[@test]
  public function typeDictionaryInstanceInterface() {
    $fixture= create('new net.xp_framework.unittest.core.generics.TypeDictionary<string>');
    $this->assertEquals(
      array(XPClass::forName('lang.Type'), Primitive::$STRING), 
      this($fixture->getClass()->getInterfaces(), 0)->genericArguments()
    );
  }

  #[@test]
  public function typeDictionaryClass() {
    $fixture= Type::forName('net.xp_framework.unittest.core.generics.TypeDictionary');
    $this->assertEquals(
      array('V'), 
      $fixture->genericComponents()
    );
  }

  #[@test]
  public function abstractTypeDictionaryClass() {
    $fixture= Type::forName('net.xp_framework.unittest.core.generics.AbstractTypeDictionary');
    $this->assertEquals(
      array('V'), 
      $fixture->genericComponents()
    );
  }

  #[@test]
  public function dictionaryInterfaceDefinition() {
    $fixture= Type::forName('net.xp_framework.unittest.core.generics.AbstractTypeDictionary');
    $this->assertEquals(
      array('K', 'V'), 
      this($fixture->getInterfaces(), 1)->genericComponents()
    );
  }

  #[@test]
  public function dictionaryInterface() {
    $fixture= Type::forName('net.xp_framework.unittest.core.generics.AbstractTypeDictionary<string>');
    $this->assertEquals(
      array(XPClass::forName('lang.Type'), Primitive::$STRING), 
      this($fixture->getInterfaces(), 1)->genericArguments()
    );
  }

  #[@test]
  public function closed() {
    $this->assertEquals(
      XPClass::forName('lang.Object'),
      Type::forName('net.xp_framework.unittest.core.generics.ListOf<string>')->getParentclass()
    );
  }

  #[@test]
  public function closedNS() {
    $this->assertEquals(
      XPClass::forName('lang.Object'),
      Type::forName('net.xp_framework.unittest.core.generics.NSListOf<string>')->getParentclass()
    );
  }

  #[@test]
  public function partiallyClosed() {
    $this->assertEquals(
      Type::forName('net.xp_framework.unittest.core.generics.Lookup<lang.Type, string>'),
      Type::forName('net.xp_framework.unittest.core.generics.TypeLookup<string>')->getParentclass()
    );
  }

  #[@test]
  public function partiallyClosedNS() {
    $this->assertEquals(
      Type::forName('net.xp_framework.unittest.core.generics.NSLookup<lang.Type, string>'),
      Type::forName('net.xp_framework.unittest.core.generics.NSTypeLookup<string>')->getParentclass()
    );
  }
}
