<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestCase', 'lang.types.ArrayList');

  /**
   * Tests the ArrayList class
   *
   * @see      xp://lang.types.ArrayList
   * @purpose  Testcase
   */
  class ArrayListTest extends TestCase {
    public
      $list = NULL;

    /**
     * Setup method. .
     *
     */
    public function setUp() {
      $this->list= new ArrayList();
    }

    /**
     * Ensures a newly created ArrayList is empty
     *
     */
    #[@test]
    public function initiallyEmpty() {
      $this->assertEquals(0, $this->list->length);
      $this->assertEquals(0, sizeof($this->list->values));
    }

    /**
     * Ensures a newly created ArrayList is equal to another newly 
     * created ArrayList
     *
     */
    #[@test]
    public function newListsAreEqual() {
      $this->assertEquals($this->list, new ArrayList());
    }

    /**
     * Tests ArrayList is usable in foreach()
     *
     */
    #[@test]
    public function isUsableInForeach() {
      foreach (new ArrayList(0, 1, 2) as $i => $value) {
        $this->assertEquals($i, $value);
      }
      $this->assertEquals(2, $i);
    }

    /**
     * Tests ArrayList is usable in for()
     *
     */
    #[@test]
    public function isUsableInFor() {
      for ($l= new ArrayList(0, 1, 2), $i= 0; $i < $l->length; $i++) {
        $this->assertEquals($i, $l[$i]);
      }
      $this->assertEquals($l->length, $i);
    }

    /**
     * Tests ArrayList is usable in foreach() - nested
     *
     */
    #[@test]
    public function isUsableInNestedForeach() {
      $r= '';
      foreach (new ArrayList(new ArrayList(1, 2, 3), new ArrayList(4, 5, 6)) as $i => $value) {
        foreach ($value as $j => $v) {
          $r.= $i.'.'.$j.':'.$v.', ';
        }
      }
      $this->assertEquals(
        '0.0:1, 0.1:2, 0.2:3, 1.0:4, 1.1:5, 1.2:6', 
        substr($r, 0, -2)
      );
    }

    /**
     * Tests array access operator is overloaded for reading
     *
     */
    #[@test]
    public function readElement() {
      $c= new ArrayList(1, 2, 3);
      $this->assertEquals(1, $c[0]);
      $this->assertEquals(2, $c[1]);
      $this->assertEquals(3, $c[2]);
    }

    /**
     * Tests reading non-existant element
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function readNonExistantElement() {
      $c= new ArrayList();
      $c[0];
    }

    /**
     * Tests array access operator is overloaded for writing
     *
     */
    #[@test]
    public function writeElement() {
      $c= new ArrayList(1, 2, 3);
      $c[0]= 4;
      $this->assertEquals(4, $c[0]);
    }

    /**
     * Tests array access operator is overloaded for writing
     *
     */
    #[@test]
    public function modifyElement() {
      $c= new ArrayList(1, 2, 3);
      $c[2]+= 1;    // $c[2]++ does NOT work due to a bug in PHP
      $this->assertEquals(4, $c[2]);
    }

    /**
     * Tests array access operator is overloaded for adding
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function addElement() {
      $c= new ArrayList();
      $c[]= 4;
    }

    /**
     * Tests adding by supplying the next larger number
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function addToEmptyBySupplyingNextLargerNumber() {
      $c1= new ArrayList();
      $c1[0]= 4;
    }

    /**
     * Tests writing with a key that would change the array's size
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function addBySupplyingNextLargerNumber() {
      $c= new ArrayList(1, 2, 3);
      $c[4]= 4;
    }

    /**
     * Tests writing with a key of incorrect type
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function keyOfIncorrectType() {
      $c= new ArrayList(1, 2, 3);
      $c['foo']= 4;
    }

    /**
     * Tests writing with a negative key
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function negativeKey() {
      $c= new ArrayList();
      $c[-1]= 4;
    }
    
    /**
     * Tests arraylist's newInstance(int) method
     *
     */
    #[@test]
    public function newInstanceSize() {
      $a= ArrayList::newInstance(4);
      $this->assertEquals(4, $a->length);
      $a[0]= 1;
      $a[1]= 2;
      $a[2]= 3;
      $a[3]= 4;
      try {
        $a[4]= 5;
        $this->fail('Should not be able to add a fifth element');
      } catch (IndexOutOfBoundsException $expected) { }
      $this->assertEquals(4, $a->length);
    }

    /**
     * Tests arraylist's newInstance(mixed[]) method
     *
     */
    #[@test]
    public function newInstanceArray() {
      $a= ArrayList::newInstance(array(1, 2, 3, 4));
      $this->assertEquals(4, $a->length);
      $this->assertEquals(1, $a[0]);
      $this->assertEquals(2, $a[1]);
      $this->assertEquals(3, $a[2]);
      $this->assertEquals(4, $a[3]);
    }

    /**
     * Tests array access operator is overloaded for isset()
     *
     */
    #[@test]
    public function testElement() {
      $c= new ArrayList(1, 2, 3);
      $this->assertTrue(isset($c[0]));
      $this->assertFalse(isset($c[3]));
      $this->assertFalse(isset($c[-1]));
    }

    /**
     * Tests array access operator is overloaded for unset()
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function removeElement() {
      $c= new ArrayList(1, 2, 3);
      unset($c[0]);
    }

    /**
     * Tests array access operator is overloaded for unset()
     *
     */
    #[@test]
    public function intactAfterUnset() {
      $c= new ArrayList(1, 2, 3);
      try {
        unset($c[0]);
      } catch (IllegalArgumentException $expected) { }

      $this->assertEquals(3, $c->length);
    }
  }
?>
