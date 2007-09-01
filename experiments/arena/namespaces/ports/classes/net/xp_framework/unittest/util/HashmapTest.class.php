<?php
/* This class is part of the XP framework
 *
 * $Id: HashmapTest.class.php 10612 2007-06-13 15:40:31Z friebe $ 
 */

  namespace net::xp_framework::unittest::util;
 
  ::uses(
    'unittest.TestCase',
    'util.Hashmap',
    'util.Comparator'
  );

  /**
   * Test Hashmap class
   *
   * @see      xp://util.Hashmap
   * @purpose  Unit Test
   */
  class HashmapTest extends unittest::TestCase {
    public
      $map= NULL;
    
    /**
     * Setup method. Creates the map member
     *
     */
    public function setUp() {
      $this->map= new util::Hashmap();
    }
        
    /**
     * Tests the map is initially empty
     *
     */
    #[@test]
    public function initiallyEmpty() {
      $this->assertTrue($this->map->isEmpty());
    }

    /**
     * Tests map equals its clone
     *
     */
    #[@test]
    public function equalsClone() {
      $this->map->put('color', 'green');
      $this->assertTrue($this->map->equals(clone($this->map)));
    }
 
    /**
     * Tests map equals another map with the same contents
     *
     */
    #[@test]
    public function equalsOtherMapWithSameContents() {
      $other= new HashMap();
      $this->map->put('color', 'green');
      $other->put('color', 'green');
      $this->assertTrue($this->map->equals($other));
    }

    /**
     * Tests map does not equal map with different contents
     *
     */
    #[@test]
    public function doesNotEqualMapWithDifferentContents() {
      $other= new HashMap();
      $this->map->put('color', 'green');
      $other->put('color', 'pink');
      $this->assertFalse($this->map->equals($other));
    }
   
    /**
     * Tests put()
     *
     */
    #[@test]
    public function put() {
      $this->map->put('color', 'green');
      $this->assertFalse($this->map->isEmpty());
      $this->assertEquals(1, $this->map->size());
    }

    /**
     * Tests get()
     *
     */
    #[@test]
    public function get() {
      $this->map->put('key', 'value');
      $this->assertEquals('value', $this->map->get('key'));
    }

    /**
     * Tests remove()
     *
     */
    #[@test]
    public function remove() {
      $this->map->put('key', 'value');
      $this->map->remove('key');
      $this->assertTrue($this->map->isEmpty());
    }

    /**
     * Tests get() returns NULL if the list is empty
     *
     */
    #[@test]
    public function getReturnsNullOnEmptyList() {
      $this->assertTrue($this->map->isEmpty());
      $this->assertNull($this->map->get('key'));
    }

    /**
     * Tests containsKey() method
     *
     */
    #[@test]
    public function containsKey() {
      $this->map->put('key', 'value');
      $this->assertTrue($this->map->containsKey('key'));
      $this->assertFalse($this->map->containsKey('non-existant-key'));
    }
    
    /**
     * Helper method for merge* test methods
     *
     * @param   bool recursive default FALSE Merge hashmaps recursively
     * @param   array<mixed, mixed> toMerge
     * @param   array<mixed, mixed> expect
     */
    protected function testMerge($recursive, $toMerge, $expect) {
      $this->map->put('color', 'red');
      $this->map->put('count', 5);

      $this->map->merge($toMerge, $recursive);
      $this->assertEquals($expect, $this->map->toArray());
    }

    /**
     * Tests merge() method
     *
     */
    #[@test]
    public function merge() {
      $this->testMerge(
        FALSE,
        array('color' => 'green', 'key' => 'value'),
        array('color' => 'red', 'key' => 'value', 'count' => 5)
      );
    }

    /**
     * Tests merge() method, using recursive behaviour.
     *
     */
    #[@test]
    public function mergeRecursive() {
      $this->testMerge(
        TRUE,
        array('color' => 'green', 'key' => 'value'),
        array('color' => array('green', 'red'), 'key' => 'value', 'count' => 5)
      );
    }
    
    /**
     * Tests merge() method when given anything besides an array or a Hashmap
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]    
    public function mergeWithIllegalArgument() {
      $this->map->merge(new lang::Object());
    }

    /**
     * Tests swap() method
     *
     */
    #[@test]
    public function swap() {
      $this->map->put('color', 'purple');
      $this->map->put('price', 15);

      $this->assertTrue($this->map->swap('color', 'price'));
      $this->assertEquals(
        array('color' => 15, 'price' => 'purple'), 
        $this->map->toArray()
      );
    }

    /**
     * Tests swap() method
     *
     */
    #[@test]
    public function swapNonExistantKeys() {
      $this->map->put('color', 'purple');
      $this->map->put('price', 15);

      $this->assertFalse($this->map->swap('color', 'non-existant-key'));
      $this->assertFalse($this->map->swap('non-existant-key', 'color'));
    }

    /**
     * Tests flip() method
     *
     */
    #[@test]
    public function flip() {
      $this->map->put('color', 'purple');
      $this->map->put('price', 15);

      $this->assertTrue($this->map->flip());
      $this->assertEquals(
        array('purple' => 'color', 15 => 'price'), 
        $this->map->toArray()
      );
    }

    /**
     * Tests clear() method
     *
     */
    #[@test]
    public function clear() {
      $this->map->put('key', 'value');
      $this->map->clear();
      $this->assertTrue($this->map->isEmpty());
    }

    /**
     * Tests containsValue() method
     *
     */
    #[@test]
    public function containsValue() {
      $this->map->put('key', 'value');
      $this->assertTrue($this->map->containsValue($v= 'value'));
      $this->assertFalse($this->map->containsValue($v= 'non-existant-value'));
    }

    /**
     * Tests values() method
     *
     */
    #[@test]
    public function keys() {
      $this->map->put('one', 1);
      $this->map->put('two', 2);
      $this->assertEquals(array('one', 'two'), $this->map->keys());
    }

    /**
     * Tests values() method
     *
     */
    #[@test]
    public function values() {
      $this->map->put('one', 1);
      $this->map->put('two', 2);
      $this->assertEquals(array(1, 2), $this->map->values());
    }

    /**
     * Tests filter() method
     *
     */
    #[@test]
    public function filter() {
      $this->map->put('one', 1);
      $this->map->put('two', 2);
      $this->map->put('three', 3);
      $this->map->put('four', 4);
      $this->map->filter(create_function('$v', 'return 1 == $v % 2;'));
      $this->assertEquals(array('one' => 1, 'three' => 3), $this->map->toArray());
    }
 
    /**
     * Tests sort() method
     *
     */
    #[@test]
    public function sort() {
      $this->map->put('two', 2);
      $this->map->put('one', 1);
      $this->map->sort(SORT_NUMERIC);

      $this->assertEquals(
        array('one' => 1, 'two' => 2), 
        $this->map->toArray()
      );
    }

    /**
     * Tests rsort() method
     *
     */
    #[@test]
    public function rsort() {
      $this->map->put('one', 1);
      $this->map->put('two', 2);
      $this->map->rsort(SORT_NUMERIC);

      $this->assertEquals(
        array('two' => 2, 'one' => 1), 
        $this->map->toArray()
      );
    }

    /**
     * Tests usort() method
     *
     */
    #[@test]
    public function usort() {
      $this->map->put('one', 'One');
      $this->map->put('two', 'two');
      $this->map->put('eins', 'one');

      $this->map->usort(::newinstance('util.Comparator', array(), '{
        function compare($a, $b) { 
          return strcasecmp($a, $b); 
        }
      }'));
      $this->assertEquals(
        array('one' => 'One', 'eins' => 'one', 'two' => 'two'), 
        $this->map->toArray()
      );
    }

    /**
     * Tests iteration
     *
     */
    #[@test]
    public function valueIteration() {
      $this->map->put('one', 1);
      $this->map->put('two', 2);
      $this->map->put('three', 3);
      for ($it= $this->map->iterator(), $i= 1; $it->hasNext(); ) {
        $this->assertEquals($i, $it->next());
        $i++;
      }
      $this->assertEquals(4, $i);
    }    

    /**
     * Tests iteration
     *
     */
    #[@test]
    public function keyIteration() {
      $this->map->put(1, 'one');
      $this->map->put(2, 'two');
      $this->map->put(3, 'three');
      for ($it= $this->map->keyIterator(), $i= 1; $it->hasNext(); ) {
        $this->assertEquals($i, $it->next());
        $i++;
      }
      $this->assertEquals(4, $i);
    }    
  }
?>
