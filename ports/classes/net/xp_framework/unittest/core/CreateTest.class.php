<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'util.collections.Vector',
    'util.collections.HashTable'
  );

  /**
   * TestCase for create() core functionality
   *
   * @purpose  Unittest
   */
  class CreateTest extends TestCase {
  
    /**
     * Test create() returns an object passed in, for use in fluent
     * interfaces, e.g.
     *
     * <code>
     *   $c= create(new Criteria())->add('bz_id', 20000, EQUAL);
     * </code>
     *
     * @see   http://news.xp-framework.net/article/184/2007/05/06/
     */
    #[@test]
    public function createReturnsObjects() {
      $fixture= new Object();
      $this->assertEquals($fixture, create($fixture));
    }

    /**
     * Test create() using short class names
     *
     */
    #[@test]
    public function createWithShortNames() {
      $h= create('new HashTable<String, String>');
      $this->assertEquals(array('String', 'String'), $h->__generic);
    }

    /**
     * Test create() using short class names
     *
     */
    #[@test]
    public function createInvokesConstructor() {
      $this->assertEquals(
        new String('Hello'), 
        create('new util.collections.Vector<lang.types.String>', array(new String('Hello')))->get(0)
      );
    }

    /**
     * Test create() using fully qualified class names
     *
     */
    #[@test]
    public function createWithQualifiedNames() {
      $h= create('new util.collections.HashTable<lang.types.String, lang.types.String>');
      $this->assertEquals(
        array(xp::reflect('lang.types.String'), xp::reflect('lang.types.String')), 
        $h->__generic
      );
    }

    /**
     * Test create() with non-generic classes
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function createWithNonGeneric() {
      create('new lang.Object<String>');
    }
  }
?>
