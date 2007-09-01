<?php
/* This class is part of the XP framework
 *
 * $Id: GenericsTest.class.php 10947 2007-08-24 10:58:03Z friebe $ 
 */

  namespace net::xp_framework::unittest::util::collections;

  ::uses(
    'util.collections.HashTable', 
    'util.collections.HashSet', 
    'util.collections.Vector',
    'util.collections.Stack',
    'util.collections.Queue',
    'util.collections.LRUBuffer'
  );

  /**
   * TestCase
   *
   * @see      xp://util.collections.HashTable 
   * @see      xp://util.collections.HashSet 
   * @see      xp://util.collections.Vector
   * @see      xp://util.collections.Stack
   * @see      xp://util.collections.Queue
   * @see      xp://util.collections.LRUBuffer
   * @purpose  Unittest
   */
  class GenericsTest extends unittest::TestCase {

    /**
     * Tests non-generic objects
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function nonGenericPassedToCreate() {
      ::create('new Object<String>');
    }
  
    /**
     * Tests HashTable<String, String>
     *
     */
    #[@test]
    public function stringStringHash() {
      ::create('new HashTable<String, String>')->put('hello', new lang::types::String('World'));
    }

    /**
     * Tests HashTable<String, String>
     *
     */
    #[@test]
    public function getFromStringStringHash() {
      ::with ($h= ::create('new HashTable<String, String>')); {
        $h->put('hello', new lang::types::String('World'));
        $this->assertEquals(new lang::types::String('World'), $h->get('hello'));
      }
    }

    /**
     * Tests HashTable<String, String>
     *
     */
    #[@test]
    public function removeFromStringStringHash() {
      ::with ($h= ::create('new HashTable<String, String>')); {
        $h->put('hello', new lang::types::String('World'));
        $this->assertEquals(new lang::types::String('World'), $h->remove('hello'));
      }
    }

    /**
     * Tests HashTable<String, String>
     *
     */
    #[@test]
    public function testStringStringHash() {
      ::with ($h= ::create('new HashTable<String, String>')); {
        $h->put('hello', new lang::types::String('World'));
        $this->assertTrue($h->containsKey('hello'));
      }
    }

    /**
     * Tests HashTable<String, String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringStringHashIllegalValue() {
      ::create('new HashTable<String, String>')->put('hello', new lang::types::Integer(1));
    }

    /**
     * Tests HashTable<String, String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringStringHashIllegalKey() {
      ::create('new HashTable<String, String>')->put(1, new lang::types::String('World'));
    }

    /**
     * Tests Vector<String>
     *
     */
    #[@test]
    public function stringVector() {
      ::create('new Vector<String>')->add(new lang::types::String('Hi'));
    }

    /**
     * Tests Vector<String>
     *
     */
    #[@test]
    public function createStringVector() {
      $this->assertEquals(
        new lang::types::String('one'), 
        ::create('new Vector<String>', array(new lang::types::String('one')))->get(0)
      );
    }

    /**
     * Tests Vector<String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringVectorIllegalValue() {
      ::create('new Vector<String>')->add(new lang::types::Integer(1));
    }

    /**
     * Tests Vector<String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function createStringVectorWithIllegalValue() {
      ::create('new Vector<String>', array(new lang::types::Integer(1)));
    }

    /**
     * Tests Stack<String>
     *
     */
    #[@test]
    public function stringStack() {
      ::create('new util.collections.Stack<String>')->push(new lang::types::String('One'));
    }

    /**
     * Tests Stack<String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringStackIllegalValue() {
      ::create('new util.collections.Stack<String>')->push(new lang::types::Integer(1));
    }

    /**
     * Tests Queue<String>
     *
     */
    #[@test]
    public function stringQueue() {
      ::create('new util.collections.Queue<String>')->put(new lang::types::String('One'));
    }

    /**
     * Tests Queue<String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringQueueIllegalValue() {
      ::create('new util.collections.Queue<String>')->put(new lang::types::Integer(1));
    }

    /**
     * Tests LRUBuffer<String>
     *
     */
    #[@test]
    public function stringLRUBuffer() {
      ::create('new util.collections.LRUBuffer<String>', 1)->add(new lang::types::String('One'));
    }

    /**
     * Tests LRUBuffer<String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringLRUBufferIllegalValue() {
      ::create('new util.collections.LRUBuffer<String>', 1)->add(new lang::types::Integer(1));
    }

    /**
     * Tests HashSet<String>
     *
     */
    #[@test]
    public function stringHashSet() {
      ::create('new util.collections.HashSet<String>')->add(new lang::types::String('One'));
    }

    /**
     * Tests HashSet<String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringHashSetIllegalValue() {
      ::create('new util.collections.HashSet<String>')->add(new lang::types::Integer(1));
    }
  }
?>
