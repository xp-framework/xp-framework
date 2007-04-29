<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'unittest.TestCase',
    'util.collections.HashSet',
    'lang.types.String'
  );

  /**
   * Test HashSet class
   *
   * @see      xp://util.collections.HashSet
   * @purpose  Unit Test
   */
  class HashSetTest extends TestCase {
    public
      $set= NULL;
    
    /**
     * Setup method. Creates the set member
     *
     */
    public function setUp() {
      $this->set= new HashSet();
    }
        
    /**
     * Tests the set is initially empty
     *
     */
    #[@test]
    public function initiallyEmpty() {
      $this->assertTrue($this->set->isEmpty());
    }

    /**
     * Tests set equals its clone
     *
     */
    #[@test]
    public function equalsClone() {
      $this->set->add(new String('green'));
      $this->assertTrue($this->set->equals(clone($this->set)));
    }
 
    /**
     * Tests set equals another set with the same contents
     *
     */
    #[@test]
    public function equalsOtherSetWithSameContents() {
      $other= new HashSet();
      $this->set->add(new String('color'));
      $other->add(new String('color'));
      $this->assertTrue($this->set->equals($other));
    }

    /**
     * Tests set does not equal set with different contents
     *
     */
    #[@test]
    public function doesNotEqualSetWithDifferentContents() {
      $other= new HashSet();
      $this->set->add(new String('blue'));
      $other->add(new String('yellow'));
      $this->assertFalse($this->set->equals($other));
    }
   
    /**
     * Tests add()
     *
     */
    #[@test]
    public function add() {
      $this->set->add(new String('green'));
      $this->assertFalse($this->set->isEmpty());
      $this->assertEquals(1, $this->set->size());
    }

    /**
     * Tests addAll()
     *
     */
    #[@test]
    public function addAll() {
      $array= array(new String('one'), new String('two'), new String('three'));
      $this->set->addAll($array);
      $this->assertFalse($this->set->isEmpty());
      $this->assertEquals(3, $this->set->size());
    }

    /**
     * Tests addAll() uniques the array given
     *
     */
    #[@test]
    public function addAllUniques() {
      $array= array(new String('one'), new String('one'), new String('two'));
      $this->set->addAll($array);
      $this->assertFalse($this->set->isEmpty());
      $this->assertEquals(2, $this->set->size());   // String{"one"} and String{"two"}
    }

    /**
     * Tests addAll() returns TRUE if the set changed as a result if the
     * call, FALSE otherwise.
     *
     */
    #[@test]
    public function addAllReturnsWhetherSetHasChanged() {
      $array= array(new String('caffeine'), new String('nicotine'));
      $this->assertTrue($this->set->addAll($array));
      $this->assertFalse($this->set->addAll($array));
      $this->assertFalse($this->set->addAll(array(new String('caffeine'))));
      $this->assertFalse($this->set->addAll(array()));
    }

    /**
     * Tests contains() method
     *
     */
    #[@test]
    public function contains() {
      $this->set->add(new String('key'));
      $this->assertTrue($this->set->contains(new String('key')));
      $this->assertFalse($this->set->contains(new String('non-existant-key')));
    }

    /**
     * Tests add() returns TRUE if the set did not already contain the
     * given element, FALSE otherwise
     *
     */
    #[@test]
    public function addSameValueTwice() {
      $color= new String('green');
      $this->assertTrue($this->set->add($color));
      $this->assertFalse($this->set->add($color));
    }

    /**
     * Tests remove()
     *
     */
    #[@test]
    public function remove() {
      $this->set->add(new String('key'));
      $this->assertTrue($this->set->remove(new String('key')));
      $this->assertTrue($this->set->isEmpty());
    }

    /**
     * Tests remove() returns FALSE when given object cannot be 
     * contained in the set (because the set is empty)
     *
     */
    #[@test]
    public function removeOnEmptySet() {
      $this->assertFalse($this->set->remove(new String('irrelevant-set-is-empty-anyway')));
    }

    /**
     * Tests remove() returns FALSE when given object is not contained
     * in the set.
     *
     */
    #[@test]
    public function removeNonExistantObject() {
      $this->set->add(new String('key'));
      $this->assertFalse($this->set->remove(new String('non-existant-key')));
    }

    /**
     * Tests clear() method
     *
     */
    #[@test]
    public function clear() {
      $this->set->add(new String('key'));
      $this->set->clear();
      $this->assertTrue($this->set->isEmpty());
    }

    /**
     * Tests toArray() method
     *
     */
    #[@test]
    public function toArray() {
      $color= new String('red');
      $this->set->add($color);
      $this->assertEquals(array($color), $this->set->toArray());
    }

    /**
     * Tests toArray() method
     *
     */
    #[@test]
    public function toArrayOnEmptySet() {
      $this->assertEquals(array(), $this->set->toArray());
    }

    /**
     * Tests toString() method
     *
     */
    #[@test]
    public function stringRepresentation() {
      $this->set->add(new String('color'));
      $this->set->add(new String('price'));
      $this->assertEquals(
        "util.collections.HashSet[2] {\n  color,\n  price\n}",
        $this->set->toString()
      );
    }

    /**
     * Tests toString() method on an empty set
     *
     */
    #[@test]
    public function stringRepresentationOfEmptySet() {
      $this->assertEquals(
        'util.collections.HashSet[0] { }',
        $this->set->toString()
      );
    }
  }
?>
