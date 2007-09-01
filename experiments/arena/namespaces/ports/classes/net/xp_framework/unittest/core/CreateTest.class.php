<?php
/* This class is part of the XP framework
 *
 * $Id: CreateTest.class.php 10954 2007-08-25 10:54:21Z friebe $ 
 */

  namespace net::xp_framework::unittest::core;

  ::uses(
    'unittest.TestCase',
    'util.collections.HashTable'
  );

  /**
   * TestCase for create() core functionality
   *
   * @purpose  Unittest
   */
  class CreateTest extends unittest::TestCase {
  
    /**
     * Test create() returns an object passed in, for use in fluent
     * interfaces, e.g.
     *
     * <code>
     *   $c= create(new Criteria())->add('bz_id', 20000, EQUAL);
     * </code>
     *
     * @see   http://xp-framework.info/xml/xp.en_US/news/view?184
     */
    #[@test]
    public function createReturnsObjects() {
      $fixture= new lang::Object();
      $this->assertEquals($fixture, ::create($fixture));
    }

    /**
     * Test create() using short class names
     *
     */
    #[@test]
    public function createWithShortNames() {
      $h= ::create('new HashTable<String, String>');
      $this->assertEquals(array('String', 'String'), $h->__generic);
    }

    /**
     * Test create() using fully qualified class names
     *
     */
    #[@test]
    public function createWithQualifiedNames() {
      $h= ::create('new util.collections.HashTable<lang.types.String, lang.types.String>');
      $this->assertEquals(array('String', 'String'), $h->__generic);
    }

    /**
     * Test create() with non-generic classes
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function createWithNonGeneric() {
      ::create('new lang.Object<String>');
    }
  }
?>
