<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'lang.types.ArrayList',
    'util.collections.HashTable',
    'util.collections.Vector',
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
      foreach (new ArrayList(0, 1, 2) as $i => $value) {
        $this->assertEquals($i, $value);
      }
      $this->assertEquals(2, $i);
    }

    /**
     * Tests ArrayList is usable in foreach() - nested
     *
     */
    #[@test]
    public function arrayListIsUsableInNestedForeach() {
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
    public function arrayListReadElement() {
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
      $c= new ArrayList(1, 2, 3);
      $c[0]= 4;
      $this->assertEquals(4, $c[0]);
    }

    /**
     * Tests array access operator is overloaded for adding
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function arrayListAddElement() {
      $c= new ArrayList();
      $c[]= 4;
    }

    /**
     * Tests adding by supplying the next larger number
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function arrayListAddToEmptyBySupplyingNextLargerNumber() {
      $c1= new ArrayList();
      $c1[0]= 4;
    }


    /**
     * Tests writing with a key that would create a gap in the array
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function arrayListAddBySupplyingNextLargerNumber2() {
      $c= new ArrayList(1, 2, 3);
      $c[4]= 4;
    }

    /**
     * Tests writing with a key of incorrect type
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function arrayListKeyOfIncorrectType() {
      $c= new ArrayList(1, 2, 3);
      $c['foo']= 4;
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
    public function arrayListRemoveElement() {
      $c= new ArrayList(1, 2, 3);
      unset($c[0]);
    }

    /**
     * Tests array access operator is overloaded for unset()
     *
     */
    #[@test]
    public function arrayListIntactAfterUnset() {
      $c= new ArrayList(1, 2, 3);
      try {
        unset($c[0]);
      } catch (IllegalArgumentException $expected) { }

      $this->assertEquals(3, $c->length);
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
     * Tests array access operator is overloaded for reading
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function hashTableReadIllegalElement() {
      $c= new HashTable();
      $c['scalar'];
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
     * Tests array access operator is overloaded for writing
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function hashTableWriteIllegalKey() {
      $c= new HashTable();
      $c['scalar']= new String('Hello');
    }

    /**
     * Tests array access operator is overloaded for writing
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function hashTableWriteIllegalValue() {
      $c= new HashTable();
      $c[new String('hello')]= 'key';
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

    /**
     * Tests array access operator is overloaded for reading
     *
     */
    #[@test]
    public function vectorReadElement() {
      $v= new Vector();
      $world= new String('world');
      $v->add($world);
      $this->assertEquals($world, $v[0]);
    }

    /**
     * Tests array access operator is overloaded for reading
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function vectorReadNonExistantElement() {
      $v= new Vector();
      $v[0];
    }

    /**
     * Tests array access operator is overloaded for adding
     *
     */
    #[@test]
    public function vectorAddElement() {
      $v= new Vector();
      $world= new String('world');
      $v[]= $world;
      $this->assertEquals($world, $v[0]);
    }
    
    /**
     * Tests array access operator is overloaded for reading
     *
     */
    #[@test]
    public function vectorWriteElement() {
      $v= new Vector();
      $world= new String('world');
      $v[0]= $world;
      $this->assertEquals($world, $v[0]);
    }

    /**
     * Tests array access operator is overloaded for isset()
     *
     */
    #[@test]
    public function vectorTestElement() {
      $v= new Vector();
      $v[]= new String('world');
      $this->assertTrue(isset($v[0]));
      $this->assertFalse(isset($v[1]));
      $this->assertFalse(isset($v[-1]));
    }

    /**
     * Tests array access operator is overloaded for unset()
     *
     */
    #[@test]
    public function vectorRemoveElement() {
      $v= new Vector();
      $v[]= new String('world');
      unset($v[0]);
      $this->assertFalse(isset($v[0]));
    }

    /**
     * Tests Vector is usable in foreach()
     *
     */
    #[@test]
    public function vectorIsUsableInForeach() {
      $values= array(new String('hello'), new String('world'));
      foreach (new Vector($values) as $i => $value) {
        $this->assertEquals($values[$i], $value);
      }
      $this->assertEquals(sizeof($values)- 1, $i);
    }
  }
?>
