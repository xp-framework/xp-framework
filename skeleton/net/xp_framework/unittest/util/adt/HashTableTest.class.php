<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'util.profiling.unittest.TestCase',
    'util.collections.HashTable',
    'text.String'
  );

  /**
   * Test HashTable class
   *
   * @see      xp://util.collections.HashTable
   * @purpose  Unit Test
   */
  class HashTableTest extends TestCase {
    var
      $map= NULL;
    
    /**
     * Setup method. Creates the map member
     *
     * @access  public
     */
    function setUp() {
      $this->map= &new HashTable();
    }
        
    /**
     * Tests the map is initially empty
     *
     * @access  public
     */
    #[@test]
    function initiallyEmpty() {
      $this->assertTrue($this->map->isEmpty());
    }

    /**
     * Tests map equals its clone
     *
     * @access  public
     */
    #[@test]
    function equalsClone() {
      $this->map->put(new String('color'), new String('green'));
      $this->assertTrue($this->map->equals(clone($this->map)));
    }
 
    /**
     * Tests map equals another map with the same contents
     *
     * @access  public
     */
    #[@test]
    function equalsOtherMapWithSameContents() {
      $other= &new HashTable();
      $this->map->put(new String('color'), new String('green'));
      $other->put(new String('color'), new String('green'));
      $this->assertTrue($this->map->equals($other));
    }

    /**
     * Tests map does not equal map with different contents
     *
     * @access  public
     */
    #[@test]
    function doesNotEqualMapWithDifferentContents() {
      $other= &new HashTable();
      $this->map->put(new String('color'), new String('blue'));
      $other->put(new String('color'), new String('yellow'));
      $this->assertFalse($this->map->equals($other));
    }
   
    /**
     * Tests put()
     *
     * @access  public
     */
    #[@test]
    function put() {
      $this->map->put(new String('color'), new String('green'));
      $this->assertFalse($this->map->isEmpty());
      $this->assertEquals(1, $this->map->size());
    }

    /**
     * Tests put() returns previous value
     *
     * @access  public
     */
    #[@test]
    function putReturnsPreviousValue() {
      $color= &new String('color');
      $this->assertNull($this->map->put($color, new String('green')));
      $this->assertEquals(new String('green'), $this->map->put($color, new String('red')));
      $this->assertEquals(new String('red'), $this->map->get($color));
    }

    /**
     * Tests get()
     *
     * @access  public
     */
    #[@test]
    function get() {
      $this->map->put(new String('key'), new String('value'));
      $this->assertEquals(new String('value'), $this->map->get(new String('key')));
    }

    /**
     * Tests get() returns NULL if the list is empty
     *
     * @access  public
     */
    #[@test]
    function getReturnsNullOnEmptyList() {
      $this->assertTrue($this->map->isEmpty());
      $this->assertNull($this->map->get(new String('key')));
    }

    /**
     * Tests remove()
     *
     * @access  public
     */
    #[@test]
    function remove() {
      $this->map->put(new String('key'), new String('value'));
      $this->map->remove(new String('key'));
      $this->assertTrue($this->map->isEmpty());
    }

    /**
     * Tests remove() returns previous value
     *
     * @access  public
     */
    #[@test]
    function removeReturnsPreviousValue() {
      $this->map->put(new String('key'), new String('value'));
      $this->assertEquals(new String('value'), $this->map->remove(new String('key')));
    }

    /**
     * Tests containsKey() method
     *
     * @access  public
     */
    #[@test]
    function containsKey() {
      $this->map->put(new String('key'), new String('value'));
      $this->assertTrue($this->map->containsKey(new String('key')));
      $this->assertFalse($this->map->containsKey(new String('non-existant-key')));
    }
    
    /**
     * Tests clear() method
     *
     * @access  public
     */
    #[@test]
    function clear() {
      $this->map->put(new String('key'), new String('value'));
      $this->map->clear();
      $this->assertTrue($this->map->isEmpty());
    }

    /**
     * Tests containsValue() method
     *
     * @access  public
     */
    #[@test]
    function containsValue() {
      $this->map->put(new String('key'), new String('value'));
      $this->assertTrue($this->map->containsValue(new String('value')));
      $this->assertFalse($this->map->containsValue(new String('non-existant-value')));
    }

    /**
     * Tests toString() method
     *
     * @access  public
     */
    #[@test]
    function stringRepresentation() {
      $this->map->put(new String('color'), new String('purple'));
      $this->map->put(new String('price'), new String('25 USD'));
      $this->assertEquals(
        "util.collections.HashTable[2] {\n  color => purple,\n  price => 25 USD\n}",
        $this->map->toString()
      );
    }

    /**
     * Tests toString() method on an empty map
     *
     * @access  public
     */
    #[@test]
    function stringRepresentationOfEmptyMap() {
      $this->assertEquals(
        'util.collections.HashTable[0] { }',
        $this->map->toString()
      );
    }
  }
?>
