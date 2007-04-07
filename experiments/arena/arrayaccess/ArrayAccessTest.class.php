<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'lang.types.ArrayList',
    'util.collections.HashTable',
    'text.String'
  );

  /**
   * TestCase
   *
   * @see      reference
   * @purpose  purpose
   */
  class ArrayAccessTest extends TestCase {

    /**
     * Tests ArrayList is usable in foreach()
     *
     */
    #[@test]
    public function arrayListIsUsableInForeach() {
      $values= array(1, 2, 3);
      foreach (new ArrayList($values) as $i => $value) {
        $this->assertEquals($values[$i], $value);
      }
      $this->assertEquals(sizeof($values)- 1, $i);
    }

    /**
     * Tests ArrayList is usable in foreach() - nested
     *
     */
    #[@test]
    public function arrayListIsUsableInNestedForeach() {
      $values= array(
        new ArrayList(array(1, 2, 3)),
        new ArrayList(array(4, 5, 6)),
      );
      $r= '';
      foreach (new ArrayList($values) as $i => $value) {
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
    public function arrayListReadElement() {
      $c= new ArrayList(array(1, 2, 3));
      $this->assertEquals(1, $c[0]);
      $this->assertEquals(2, $c[1]);
      $this->assertEquals(3, $c[2]);
    }

    /**
     * Tests reading non-existant element
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function arrayListReadNonExistantElement() {
      $c= new ArrayList();
      $c[0];
    }

    /**
     * Tests array access operator is overloaded for writing
     *
     */
    #[@test]
    public function arrayListWriteElement() {
      $c= new ArrayList(array(1, 2, 3));
      $c[0]= 4;
      $this->assertEquals(4, $c[0]);
    }

    /**
     * Tests array access operator is overloaded for adding
     *
     */
    #[@test]
    public function arrayListAddElement() {
      $c= new ArrayList();
      $c[]= 4;
      $this->assertEquals(4, $c[0]);
    }

    /**
     * Tests adding by supplying the next larger number
     *
     */
    #[@test]
    public function arrayListAddBySupplyingNextLargerNumber() {
      $c1= new ArrayList();
      $c1[0]= 4;
      $c2= new ArrayList(array(1, 2, 3));
      $c2[3]= 4;
    }

    /**
     * Tests writing with a key of incorrect type
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function arrayListKeyOfIncorrectType() {
      $c= new ArrayList(array(1, 2, 3));
      $c['foo']= 4;
    }

    /**
     * Tests writing with a key that would create a gap in the array
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function arrayListKeyWhichCreatesGap() {
      $c= new ArrayList();
      $c[1]= 4;
    }

    /**
     * Tests writing with a key that would create a gap in the array
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function arrayListKeyWhichCreatesGap2() {
      $c= new ArrayList(array(1, 2, 3));
      $c[4]= 4;   // $c[3] would work, appending a new element
    }

    /**
     * Tests writing with a negative key
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function arrayListNegativeKey() {
      $c= new ArrayList();
      $c[-1]= 4;
    }

    /**
     * Tests array access operator is overloaded for isset()
     *
     */
    #[@test]
    public function arrayListTestElement() {
      $c= new ArrayList(array(1, 2, 3));
      $this->assertTrue(isset($c[0]));
      $this->assertFalse(isset($c[3]));
      $this->assertFalse(isset($c[-1]));
    }

    /**
     * Tests array access operator is overloaded for unset()
     *
     */
    #[@test]
    public function arrayListRemoveElement() {
      $c= new ArrayList(array(1, 2, 3));
      $this->assertTrue(isset($c[0]));
      unset($c[0]);
      $this->assertFalse(isset($c[0]));
    }

    /**
     * Tests array access operator is overloaded for reading
     *
     */
    #[@test]
    public function hashTableReadElement() {
      $c= new HashTable();
      $world= new String('world');
      $c->put(new String('hello'), $world);
      $this->assertEquals($world, $c[new String('hello')]);
    }

    /**
     * Tests array access operator is overloaded for reading
     *
     */
    #[@test]
    public function hashTableReadNonExistantElement() {
      $c= new HashTable();
      $this->assertEquals(NULL, $c[new String('hello')]);
    }

    /**
     * Tests array access operator is overloaded for writing
     *
     */
    #[@test]
    public function hashTableWriteElement() {
      $c= new HashTable();
      $world= new String('world');
      $c[new String('hello')]= $world;
      $this->assertEquals($world, $c->get(new String('hello')));
    }


    /**
     * Tests array access operator is overloaded for isset()
     *
     */
    #[@test]
    public function hashTableTestElement() {
      $c= new HashTable();
      $c->put(new String('hello'), new String('world'));
      $this->assertTrue(isset($c[new String('hello')]));
      $this->assertFalse(isset($c[new String('world')]));
    }

    /**
     * Tests array access operator is overloaded for unset()
     *
     */
    #[@test]
    public function hashTableRemoveElement() {
      $c= new HashTable();
      $c->put(new String('hello'), new String('world'));
      $this->assertTrue(isset($c[new String('hello')]));
      unset($c[new String('hello')]);
      $this->assertFalse(isset($c[new String('hello')]));
    }
  }
?>
