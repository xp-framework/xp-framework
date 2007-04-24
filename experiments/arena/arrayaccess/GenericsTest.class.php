<?php
/* This class is part of the XP framework
 *
 * $Id: BoxingTest.class.php 10059 2007-04-19 10:59:14Z friebe $ 
 */

  uses('util.collections.HashTable', 'util.collections.Vector');

  /**
   * TestCase
   *
   * @see      reference
   * @purpose  purpose
   */
  class GenericsTest extends TestCase {

    /**
     * Tests non-generic objects
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function nonGenericPassedToCreate() {
      create('Object<String>');
    }
  
    /**
     * Tests HashTable<String, String>
     *
     */
    #[@test]
    public function stringStringHash() {
      create('HashTable<String, String>')->put('hello', new String('World'));
    }

    /**
     * Tests HashTable<String, String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringStringHashIllegalValue() {
      create('HashTable<String, String>')->put('hello', new Integer(1));
    }

    /**
     * Tests HashTable<String, String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringStringHashIllegalKey() {
      create('HashTable<String, String>')->put(1, new String('World'));
    }

    /**
     * Tests Vector<String>
     *
     */
    #[@test]
    public function stringVector() {
      create('Vector<String>')->add(new String('Hi'));
    }

    /**
     * Tests Vector<String>
     *
     */
    #[@test]
    public function createStringVector() {
      $this->assertEquals(
        new String('one'), 
        create('Vector<String>', array(new String('one')))->get(0)
      );
    }

    /**
     * Tests Vector<String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringVectorIllegalValue() {
      create('Vector<String>')->add(new Integer(1));
    }

    /**
     * Tests Vector<String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function createStringVectorWithIllegalValue() {
      create('Vector<String>', array(new Integer(1)));
    }
  }
?>
