<?php namespace net\xp_framework\unittest\core;

use util\collections\Vector;
use util\collections\HashTable;

/**
 * TestCase for create() core functionality. It has the following two purposes:
 *
 * 1) Create generics
 * ```php
 * $v= create('new util.collections.Vector<lang.types.String>');
 * ```
 *
 * 2) Returning an object passed in, for use in fluent interfaces, e.g.
 * ```php
 * $c= create(new Criteria())->add('bz_id', 20000, EQUAL);
 * ````
 * 
 * @see   http://news.xp-framework.net/article/184/2007/05/06/
 */
class CreateTest extends \unittest\TestCase {

  #[@test]
  public function createReturnsObjects() {
    $fixture= new \lang\Object();
    $this->assertEquals($fixture, create($fixture));
  }

  #[@test]
  public function createWithShortNames() {
    \lang\XPClass::forName('util.collections.HashTable');
    $h= create('new HashTable<String, String>');
    $this->assertEquals(
      array(\lang\XPClass::forName('lang.types.String'), \lang\XPClass::forName('lang.types.String')), 
      $h->getClass()->genericArguments()
    );
  }

  #[@test]
  public function createInvokesConstructor() {
    $this->assertEquals(
      new \lang\types\String('Hello'), 
      create('new util.collections.Vector<lang.types.String>', array(new \lang\types\String('Hello')))->get(0)
    );
  }

  #[@test]
  public function createWithQualifiedNames() {
    $h= create('new util.collections.HashTable<lang.types.String, lang.types.String>');
    $this->assertEquals(
      array(\lang\XPClass::forName('lang.types.String'), \lang\XPClass::forName('lang.types.String')), 
      $h->getClass()->genericArguments()
    );
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function createWithNonGeneric() {
    create('new lang.Object<String>');
  }
}
