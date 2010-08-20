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
     * Tests HashTable::equals()
     *
     */
    #[@test]
    public function differingGenericHashTablesNotEquals() {
      $this->assertNotEquals(
        create('new HashTable<lang.Object, lang.Object>'),
        create('new HashTable<lang.types.String, lang.Object>')
      );
    }

    /**
     * Tests HashTable::equals()
     *
     */
    #[@test]
    public function sameGenericHashTablesAreEqual() {
      $this->assertEquals(
        create('new HashTable<lang.types.String, lang.Object>'),
        create('new HashTable<lang.types.String, lang.Object>')
      );
    }

    /**
     * Tests HashSet::equals()
     *
     */
    #[@test]
    public function differingGenericHashSetsNotEquals() {
      $this->assertNotEquals(
        create('new HashSet<lang.Object>'),
        create('new HashSet<lang.types.String>')
      );
    }

    /**
     * Tests HashSet::equals()
     *
     */
    #[@test]
    public function sameGenericHashSetsAreEqual() {
      $this->assertEquals(
        create('new HashSet<lang.types.String>'),
        create('new HashSet<lang.types.String>')
      );
    }

    /**
     * Tests Vector::equals()
     *
     */
    #[@test]
    public function differingGenericVectorsNotEquals() {
      $this->assertNotEquals(
        create('new Vector<lang.Object>'),
        create('new Vector<lang.types.String>')
      );
    }

    /**
     * Tests Vector::equals()
     *
     */
    #[@test]
    public function sameGenericVectorsAreEqual() {
      $this->assertEquals(
        create('new Vector<lang.types.String>'),
        create('new Vector<lang.types.String>')
      );
    }

    /**
     * Tests Queue::equals()
     *
     */
    #[@test]
    public function differingGenericQueuesNotEquals() {
      $this->assertNotEquals(
        create('new Queue<lang.Object>'),
        create('new Queue<lang.types.String>')
      );
    }

    /**
     * Tests Queue::equals()
     *
     */
    #[@test]
    public function sameGenericQueuesAreEqual() {
      $this->assertEquals(
        create('new Queue<lang.types.String>'),
        create('new Queue<lang.types.String>')
      );
    }

    /**
     * Tests Stack::equals()
     *
     */
    #[@test]
    public function differingGenericStacksNotEquals() {
      $this->assertNotEquals(
        create('new Stack<lang.Object>'),
        create('new Stack<lang.types.String>')
      );
    }

    /**
     * Tests Stack::equals()
     *
     */
    #[@test]
    public function sameGenericStacksAreEqual() {
      $this->assertEquals(
        create('new Stack<lang.types.String>'),
        create('new Stack<lang.types.String>')
      );
    }

    /**
     * Tests LRUBuffer::equals()
     *
     */
    #[@test]
    public function differingGenericLRUBuffersNotEquals() {
      $this->assertNotEquals(
        create('new LRUBuffer<lang.Object>', array(10)),
        create('new LRUBuffer<lang.types.String>', array(10))
      );
    }

    /**
     * Tests LRUBuffer::equals()
     *
     */
    #[@test]
    public function sameGenericLRUBuffersAreEqual() {
      $this->assertEquals(
        create('new LRUBuffer<lang.types.String>', array(10)),
        create('new LRUBuffer<lang.types.String>', array(10))
      );
    }

    /**
     * Tests non-generic objects
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function nonGenericPassedToCreate() {
      create('new lang.Object<lang.types.String>');
    }
  
    /**
     * Tests HashTable<lang.types.String, lang.types.String>
     *
     */
    #[@test]
    public function stringStringHash() {
      create('new util.collections.HashTable<lang.types.String, lang.types.String>')->put('hello', new String('World'));
    }

    /**
     * Tests HashTable<lang.types.String, lang.types.String>
     *
     */
    #[@test]
    public function getFromStringStringHash() {
      with ($h= create('new util.collections.HashTable<lang.types.String, lang.types.String>')); {
        $h->put('hello', new String('World'));
        $this->assertEquals(new String('World'), $h->get('hello'));
      }
    }

    /**
     * Tests HashTable<lang.types.String, lang.types.String>
     *
     */
    #[@test]
    public function removeFromStringStringHash() {
      with ($h= create('new util.collections.HashTable<lang.types.String, lang.types.String>')); {
        $h->put('hello', new String('World'));
        $this->assertEquals(new String('World'), $h->remove('hello'));
      }
    }

    /**
     * Tests HashTable<lang.types.String, lang.types.String>
     *
     */
    #[@test]
    public function testStringStringHash() {
      with ($h= create('new util.collections.HashTable<lang.types.String, lang.types.String>')); {
        $h->put('hello', new String('World'));
        $this->assertTrue($h->containsKey('hello'));
      }
    }

    /**
     * Tests HashTable<lang.types.String, lang.types.String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringStringHashPutIllegalValue() {
      create('new util.collections.HashTable<lang.types.String, lang.types.String>')->put('hello', new Integer(1));
    }

    /**
     * Tests HashTable<lang.types.String, lang.types.String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringStringHashGetIllegalValue() {
      create('new util.collections.HashTable<lang.types.String, lang.types.String>')->get(new Integer(1));
    }

    /**
     * Tests HashTable<lang.types.String, lang.types.String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringStringHashRemoveIllegalValue() {
      create('new util.collections.HashTable<lang.types.String, lang.types.String>')->remove(new Integer(1));
    }

    /**
     * Tests HashTable<lang.types.String, lang.types.String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringStringHashContainsKeyIllegalValue() {
      create('new util.collections.HashTable<lang.types.String, lang.types.String>')->containsKey(new Integer(1));
    }

    /**
     * Tests HashTable<lang.types.String, lang.types.String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringStringHashContainsValueIllegalValue() {
      create('new util.collections.HashTable<lang.types.String, lang.types.String>')->containsValue(new Integer(1));
    }

    /**
     * Tests HashTable<lang.types.String, lang.types.String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringStringHashIllegalKey() {
      create('new util.collections.HashTable<lang.types.String, lang.types.String>')->put(1, new String('World'));
    }

    /**
     * Tests Vector<lang.types.String>
     *
     */
    #[@test]
    public function stringVector() {
      create('new util.collections.Vector<lang.types.String>')->add(new String('Hi'));
    }

    /**
     * Tests Vector<lang.types.String>
     *
     */
    #[@test]
    public function createStringVector() {
      $this->assertEquals(
        new String('one'), 
        create('new util.collections.Vector<lang.types.String>', array(new String('one')))->get(0)
      );
    }

    /**
     * Tests Vector<lang.types.String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringVectorAddIllegalValue() {
      create('new util.collections.Vector<lang.types.String>')->add(new Integer(1));
    }

    /**
     * Tests Vector<lang.types.String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringVectorSetIllegalValue() {
      create('new util.collections.Vector<lang.types.String>', array(new String('')))->set(0, new Integer(1));
    }

    /**
     * Tests Vector<lang.types.String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringVectorContainsIllegalValue() {
      create('new util.collections.Vector<lang.types.String>')->contains(new Integer(1));
    }

    /**
     * Tests Vector<lang.types.String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function createStringVectorWithIllegalValue() {
      create('new util.collections.Vector<lang.types.String>', array(new Integer(1)));
    }

    /**
     * Tests Stack<lang.types.String>
     *
     */
    #[@test]
    public function stringStack() {
      create('new util.collections.Stack<lang.types.String>')->push(new String('One'));
    }

    /**
     * Tests Stack<lang.types.String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringStackPushIllegalValue() {
      create('new util.collections.Stack<lang.types.String>')->push(new Integer(1));
    }

    /**
     * Tests Stack<lang.types.String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringStackSearchIllegalValue() {
      create('new util.collections.Stack<lang.types.String>')->search(new Integer(1));
    }

    /**
     * Tests Queue<lang.types.String>
     *
     */
    #[@test]
    public function stringQueue() {
      create('new util.collections.Queue<lang.types.String>')->put(new String('One'));
    }

    /**
     * Tests Queue<lang.types.String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringQueuePutIllegalValue() {
      create('new util.collections.Queue<lang.types.String>')->put(new Integer(1));
    }

    /**
     * Tests Queue<lang.types.String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringQueueSearchIllegalValue() {
      create('new util.collections.Queue<lang.types.String>')->search(new Integer(1));
    }

    /**
     * Tests Queue<lang.types.String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringQueueRemoveIllegalValue() {
      create('new util.collections.Queue<lang.types.String>')->remove(new Integer(1));
    }

    /**
     * Tests LRUBuffer<lang.types.String>
     *
     */
    #[@test]
    public function stringLRUBuffer() {
      create('new util.collections.LRUBuffer<lang.types.String>', 1)->add(new String('One'));
    }

    /**
     * Tests LRUBuffer<lang.types.String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringLRUBufferAddIllegalValue() {
      create('new util.collections.LRUBuffer<lang.types.String>', 1)->add(new Integer(1));
    }

    /**
     * Tests LRUBuffer<lang.types.String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringLRUBufferUpdateIllegalValue() {
      create('new util.collections.LRUBuffer<lang.types.String>', 1)->update(new Integer(1));
    }

    /**
     * Tests HashSet<lang.types.String>
     *
     */
    #[@test]
    public function stringHashSet() {
      create('new util.collections.HashSet<lang.types.String>')->add(new String('One'));
    }

    /**
     * Tests HashSet<lang.types.String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringHashSetAddIllegalValue() {
      create('new util.collections.HashSet<lang.types.String>')->add(new Integer(1));
    }

    /**
     * Tests HashSet<lang.types.String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringHashSetContainsIllegalValue() {
      create('new util.collections.HashSet<lang.types.String>')->contains(new Integer(1));
    }

    /**
     * Tests HashSet<lang.types.String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringHashSetRemoveIllegalValue() {
      create('new util.collections.HashSet<lang.types.String>')->remove(new Integer(1));
    }

    /**
     * Tests HashSet<lang.types.String>
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringHashSetAddAllIllegalValue() {
      create('new util.collections.HashSet<lang.types.String>')->addAll(array(
        new String('HELLO'),    // Still OK
        new Integer(2),         // Blam
      ));
    }

    /**
     * Tests HashSet<lang.types.String>
     *
     */
    #[@test]
    public function stringHashSetUnchangedAferAddAllIllegalValue() {
      $h= create('new util.collections.HashSet<lang.types.String>');
      try {
        $h->addAll(array(new String('HELLO'), new Integer(2)));
      } catch (IllegalArgumentException $expected) {
      }
      $this->assertTrue($h->isEmpty());
    }
  }
?>
