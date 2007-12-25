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
   * @see      xp://util.collections.HashTable 
   * @see      xp://util.collections.HashSet 
   * @see      xp://util.collections.Vector
   * @see      xp://util.collections.Stack
   * @see      xp://util.collections.Queue
   * @see      xp://util.collections.LRUBuffer
   * @purpose  Unittest
   */
  class GenericsTest extends TestCase {

    /**
     * Tests non-generic objects
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function nonGenericPassedToCreate() {
      create('new Object<String>');
    }
  
    /**
     * Tests HashTable<String, String>
     *
     */
    #[@test]
    public function stringStringHash() {
      create('new HashTable<String, String>')->put('hello', new String('World'));
    }

    /**
     * Tests HashTable<String, String>
     *
     */
    #[@test]
    public function getFromStringStringHash() {
      with ($h= create('new HashTable<String, String>')); {
        $h->put('hello', new String('World'));
        $this->assertEquals(new String('World'), $h->get('hello'));
      }
    }

    /**
     * Tests HashTable<String, String>
     *
     */
    #[@test]
    public function removeFromStringStringHash() {
      with ($h= create('new HashTable<String, String>')); {
        $h->put('hello', new String('World'));
        $this->assertEquals(new String('World'), $h->remove('hello'));
      }
    }

    /**
     * Tests HashTable<String, String>
     *
     */
    #[@test]
    public function testStringStringHash() {
      with ($h= create('new HashTable<String, String>')); {
        $h->put('hello', new String('World'));
        $this->assertTrue($h->containsKey('hello'));
      }
    }

    /**
     * Tests HashTable<String, String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringStringHashPutIllegalValue() {
      create('new HashTable<String, String>')->put('hello', new Integer(1));
    }

    /**
     * Tests HashTable<String, String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringStringHashGetIllegalValue() {
      create('new HashTable<String, String>')->get(new Integer(1));
    }

    /**
     * Tests HashTable<String, String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringStringHashRemoveIllegalValue() {
      create('new HashTable<String, String>')->remove(new Integer(1));
    }

    /**
     * Tests HashTable<String, String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringStringHashContainsKeyIllegalValue() {
      create('new HashTable<String, String>')->containsKey(new Integer(1));
    }

    /**
     * Tests HashTable<String, String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringStringHashContainsValueIllegalValue() {
      create('new HashTable<String, String>')->containsValue(new Integer(1));
    }

    /**
     * Tests HashTable<String, String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringStringHashIllegalKey() {
      create('new HashTable<String, String>')->put(1, new String('World'));
    }

    /**
     * Tests Vector<String>
     *
     */
    #[@test]
    public function stringVector() {
      create('new Vector<String>')->add(new String('Hi'));
    }

    /**
     * Tests Vector<String>
     *
     */
    #[@test]
    public function createStringVector() {
      $this->assertEquals(
        new String('one'), 
        create('new Vector<String>', array(new String('one')))->get(0)
      );
    }

    /**
     * Tests Vector<String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringVectorAddIllegalValue() {
      create('new Vector<String>')->add(new Integer(1));
    }

    /**
     * Tests Vector<String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringVectorContainsIllegalValue() {
      create('new Vector<String>')->contains(new Integer(1));
    }

    /**
     * Tests Vector<String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringVectorRemoveIllegalValue() {
      create('new Vector<String>')->remove(new Integer(1));
    }

    /**
     * Tests Vector<String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function createStringVectorWithIllegalValue() {
      create('new Vector<String>', array(new Integer(1)));
    }

    /**
     * Tests Stack<String>
     *
     */
    #[@test]
    public function stringStack() {
      create('new util.collections.Stack<String>')->push(new String('One'));
    }

    /**
     * Tests Stack<String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringStackIllegalValue() {
      create('new util.collections.Stack<String>')->push(new Integer(1));
    }

    /**
     * Tests Queue<String>
     *
     */
    #[@test]
    public function stringQueue() {
      create('new util.collections.Queue<String>')->put(new String('One'));
    }

    /**
     * Tests Queue<String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringQueueIllegalValue() {
      create('new util.collections.Queue<String>')->put(new Integer(1));
    }

    /**
     * Tests LRUBuffer<String>
     *
     */
    #[@test]
    public function stringLRUBuffer() {
      create('new util.collections.LRUBuffer<String>', 1)->add(new String('One'));
    }

    /**
     * Tests LRUBuffer<String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringLRUBufferIllegalValue() {
      create('new util.collections.LRUBuffer<String>', 1)->add(new Integer(1));
    }

    /**
     * Tests HashSet<String>
     *
     */
    #[@test]
    public function stringHashSet() {
      create('new util.collections.HashSet<String>')->add(new String('One'));
    }

    /**
     * Tests HashSet<String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringHashSetAddIllegalValue() {
      create('new util.collections.HashSet<String>')->add(new Integer(1));
    }

    /**
     * Tests HashSet<String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringHashSetContainsIllegalValue() {
      create('new util.collections.HashSet<String>')->contains(new Integer(1));
    }

    /**
     * Tests HashSet<String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringHashSetRemoveIllegalValue() {
      create('new util.collections.HashSet<String>')->remove(new Integer(1));
    }

    /**
     * Tests HashSet<String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringHashSetAddAllIllegalValue() {
      create('new util.collections.HashSet<String>')->addAll(array(
        new String('HELLO'),    // Still OK
        new Integer(2),         // Blam
      ));
    }

    /**
     * Tests HashSet<String>
     *
     */
    #[@test]
    public function stringHashSetUnchangedAferAddAllIllegalValue() {
      $h= create('new util.collections.HashSet<String>');
      try {
        $h->addAll(array(new String('HELLO'), new Integer(2)));
      } catch (IllegalArgumentException $expected) {
      }
      $this->assertTrue($h->isEmpty());
    }
  }
?>
