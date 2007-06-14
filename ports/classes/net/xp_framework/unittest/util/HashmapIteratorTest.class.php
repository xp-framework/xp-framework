<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'unittest.TestCase',
    'util.Hashmap',
    'util.HashmapIterator',
    'util.Comparator'
  );

  /**
   * Test Hashmap class
   *
   * @see      xp://util.Hashmap
   * @purpose  Unit Test
   */
  class HashmapIteratorTest extends TestCase {
    
    /**
     * Setup method. Creates the map
     *
     */
    public function setUp() {
      $this->map= new Hashmap(
        array(
          'k1' => 'v1',
          'k2' => 'v2',
          'k3' => 'v3',
        )
      );
    }
        
    /**
     * Tests next with empty Hashmap
     *
     */
    #[@test, @expect('util.NoSuchElementException')]
    public function nextOnEmpty() {
      create(new HashmapIterator(array()))->next();
    }

    /**
     * Tests hasNext with empty Hashmap
     *
     */
    #[@test]
    public function hasNextOnEmpty() {
      $this->assertFalse(create(new HashmapIterator(array()))->hasNext());
    }

    /**
     * Tests next with empty Hashmap
     *
     */
    #[@test]
    public function nextOnArray() {
      $this->assertEquals(
        'v1',
        create(new HashmapIterator(array('k1' => 'v1')))->next()
      );
    }

    /**
     * Tests hasNext with empty Hashmap
     *
     */
    #[@test]
    public function hasNextOnArray() {
      $this->asserttrue(
        create(new HashmapIterator(array('k1' => 'v1')))->hasNext()
      );
    }

    /**
     * Tests next with empty Hashmap
     *
     */
    #[@test]
    public function nextFromHashmap() {
      $this->assertEquals(
        'v1',
        $this->map->iterator()->next()
      );
    }

    /**
     * Tests next with empty Hashmap
     *
     */
    #[@test]
    public function nextFromHashmapKeys() {
      $this->assertEquals(
        'k1',
        $this->map->keyIterator()->next()
      );
    }

    /**
     * Tests next with empty Hashmap
     *
     */
    #[@test, @expect('util.NoSuchElementException')]
    public function nextOnEnd() {
      $i= $this->map->iterator();
      $i->next();
      $i->next();
      $i->next();
      $i->next();
    }

    /**
     * Tests next with empty Hashmap
     *
     */
    #[@test]
    public function hasNextOnEnd() {
      $i= $this->map->iterator();
      $i->next();
      $i->next();
      $i->next();
      $this->assertFalse($i->hasNext());
    }

  }
?>
