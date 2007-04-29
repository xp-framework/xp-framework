<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
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

    /**
     * Tests Stack<String>
     *
     */
    #[@test]
    public function stringStack() {
      create('util.collections.Stack<String>')->push(new String('One'));
    }

    /**
     * Tests Stack<String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringStackIllegalValue() {
      create('util.collections.Stack<String>')->push(new Integer(1));
    }

    /**
     * Tests Queue<String>
     *
     */
    #[@test]
    public function stringQueue() {
      create('util.collections.Queue<String>')->put(new String('One'));
    }

    /**
     * Tests Queue<String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringQueueIllegalValue() {
      create('util.collections.Queue<String>')->put(new Integer(1));
    }

    /**
     * Tests LRUBuffer<String>
     *
     */
    #[@test]
    public function stringLRUBuffer() {
      create('util.collections.LRUBuffer<String>', 1)->add(new String('One'));
    }

    /**
     * Tests LRUBuffer<String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringLRUBufferIllegalValue() {
      create('util.collections.LRUBuffer<String>', 1)->add(new Integer(1));
    }

    /**
     * Tests HashSet<String>
     *
     */
    #[@test]
    public function stringHashSet() {
      create('util.collections.HashSet<String>')->add(new String('One'));
    }

    /**
     * Tests HashSet<String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringHashSetIllegalValue() {
      create('util.collections.HashSet<String>')->add(new Integer(1));
    }
  }
?>
