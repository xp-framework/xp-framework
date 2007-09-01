<?php
/* This class is part of the XP framework
 *
 * $Id: HashTableTest.class.php 10175 2007-04-29 17:13:09Z friebe $
 */

  namespace net::xp_framework::unittest::util::collections;
 
  ::uses(
    'unittest.TestCase',
    'util.collections.HashTable',
    'lang.types.String'
  );

  /**
   * Test HashTable class
   *
   * @see      xp://util.collections.HashTable
   * @purpose  Unit Test
   */
  class HashTableTest extends unittest::TestCase {
    public
      $map= NULL;
    
    /**
     * Setup method. Creates the map member
     *
     */
    public function setUp() {
      $this->map= new util::collections::HashTable();
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
      $this->map->put(new lang::types::String('color'), new lang::types::String('green'));
      $this->assertTrue($this->map->equals(clone($this->map)));
    }
 
    /**
     * Tests map equals another map with the same contents
     *
     */
    #[@test]
    public function equalsOtherMapWithSameContents() {
      $other= new util::collections::HashTable();
      $this->map->put(new lang::types::String('color'), new lang::types::String('green'));
      $other->put(new lang::types::String('color'), new lang::types::String('green'));
      $this->assertTrue($this->map->equals($other));
    }

    /**
     * Tests map does not equal map with different contents
     *
     */
    #[@test]
    public function doesNotEqualMapWithDifferentContents() {
      $other= new util::collections::HashTable();
      $this->map->put(new lang::types::String('color'), new lang::types::String('blue'));
      $other->put(new lang::types::String('color'), new lang::types::String('yellow'));
      $this->assertFalse($this->map->equals($other));
    }
   
    /**
     * Tests put()
     *
     */
    #[@test]
    public function put() {
      $this->map->put(new lang::types::String('color'), new lang::types::String('green'));
      $this->assertFalse($this->map->isEmpty());
      $this->assertEquals(1, $this->map->size());
    }

    /**
     * Tests put() returns previous value
     *
     */
    #[@test]
    public function putReturnsPreviousValue() {
      $color= new lang::types::String('color');
      $this->assertNull($this->map->put($color, new lang::types::String('green')));
      $this->assertEquals(new lang::types::String('green'), $this->map->put($color, new lang::types::String('red')));
      $this->assertEquals(new lang::types::String('red'), $this->map->get($color));
    }

    /**
     * Tests get()
     *
     */
    #[@test]
    public function get() {
      $this->map->put(new lang::types::String('key'), new lang::types::String('value'));
      $this->assertEquals(new lang::types::String('value'), $this->map->get(new lang::types::String('key')));
    }

    /**
     * Tests get() returns NULL if the list is empty
     *
     */
    #[@test]
    public function getReturnsNullOnEmptyList() {
      $this->assertTrue($this->map->isEmpty());
      $this->assertNull($this->map->get(new lang::types::String('key')));
    }

    /**
     * Tests remove()
     *
     */
    #[@test]
    public function remove() {
      $this->map->put(new lang::types::String('key'), new lang::types::String('value'));
      $this->map->remove(new lang::types::String('key'));
      $this->assertTrue($this->map->isEmpty());
    }

    /**
     * Tests remove() returns previous value
     *
     */
    #[@test]
    public function removeReturnsPreviousValue() {
      $this->map->put(new lang::types::String('key'), new lang::types::String('value'));
      $this->assertEquals(new lang::types::String('value'), $this->map->remove(new lang::types::String('key')));
    }

    /**
     * Tests containsKey() method
     *
     */
    #[@test]
    public function containsKey() {
      $this->map->put(new lang::types::String('key'), new lang::types::String('value'));
      $this->assertTrue($this->map->containsKey(new lang::types::String('key')));
      $this->assertFalse($this->map->containsKey(new lang::types::String('non-existant-key')));
    }
    
    /**
     * Tests clear() method
     *
     */
    #[@test]
    public function clear() {
      $this->map->put(new lang::types::String('key'), new lang::types::String('value'));
      $this->map->clear();
      $this->assertTrue($this->map->isEmpty());
    }

    /**
     * Tests containsValue() method
     *
     */
    #[@test]
    public function containsValue() {
      $this->map->put(new lang::types::String('key'), new lang::types::String('value'));
      $this->assertTrue($this->map->containsValue(new lang::types::String('value')));
      $this->assertFalse($this->map->containsValue(new lang::types::String('non-existant-value')));
    }

    /**
     * Tests keys() method
     *
     */
    #[@test]
    public function keys() {
      $this->map->put(new lang::types::String('key'), new lang::types::String('value'));
      $this->assertEquals(array(new lang::types::String('key')), $this->map->keys());
    }

    /**
     * Tests toString() method
     *
     */
    #[@test]
    public function stringRepresentation() {
      $this->map->put(new lang::types::String('color'), new lang::types::String('purple'));
      $this->map->put(new lang::types::String('price'), new lang::types::String('25 USD'));
      $this->assertEquals(
        "util.collections.HashTable[2] {\n  color => purple,\n  price => 25 USD\n}",
        $this->map->toString()
      );
    }

    /**
     * Tests toString() method on an empty map
     *
     */
    #[@test]
    public function stringRepresentationOfEmptyMap() {
      $this->assertEquals(
        'util.collections.HashTable[0] { }',
        $this->map->toString()
      );
    }
  }
?>
