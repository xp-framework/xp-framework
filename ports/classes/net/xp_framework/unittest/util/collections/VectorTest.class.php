<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'lang.types.String',
    'util.collections.Vector'
  );

  /**
   * TestCase for vector class
   *
   * @see      xp://util.collections.Vector
   * @purpose  Unittest
   */
  class VectorTest extends TestCase {
  
    /**
     * Test a newly created vector is empty
     *
     */
    #[@test]
    public function initiallyEmpty() {
      $this->assertTrue(create(new Vector())->isEmpty());
    }

    /**
     * Test a newly created vector is empty
     *
     */
    #[@test]
    public function sizeOfEmptyVector() {
      $this->assertEquals(0, create(new Vector())->size());
    }
    
    /**
     * Test a newly created vector is empty
     *
     */
    #[@test]
    public function nonEmptyVector() {
      $v= new Vector(array(new Object()));
      $this->assertEquals(1, $v->size());
      $this->assertFalse($v->isEmpty());
    }

    /**
     * Test adding elements
     *
     */
    #[@test]
    public function adding() {
      $v= new Vector();
      $v->add(new Object());
      $this->assertEquals(1, $v->size());
    }

    /**
     * Test adding NULL does not work
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function addingNull() {
      create(new Vector())->add(NULL);
    }

    /**
     * Test replacing elements
     *
     */
    #[@test]
    public function replacing() {
      $v= new Vector();
      $o= new String('one');
      $v->add($o);
      $r= $v->set(0, new String('two'));
      $this->assertEquals(1, $v->size());
      $this->assertEquals($o, $r);
    }

    /**
     * Test replacing elements with NULL does not work
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function replacingWithNull() {
      create(new Vector(array(new Object())))->set(0, NULL);
    }

    /**
     * Test replacing elements
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function settingPastEnd() {
      create(new Vector())->set(0, new Object());
    }

    /**
     * Test replacing elements
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function settingNegative() {
      create(new Vector())->set(-1, new Object());
    }

    /**
     * Test reading elements
     *
     */
    #[@test]
    public function reading() {
      $v= new Vector();
      $o= new String('one');
      $v->add($o);
      $r= $v->get(0);
      $this->assertEquals($o, $r);
    }

    /**
     * Test reading elements
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function readingPastEnd() {
      create(new Vector())->get(0);
    }

    /**
     * Test reading elements
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function readingNegative() {
      create(new Vector())->get(-1);
    }

    /**
     * Test removing elements
     *
     */
    #[@test]
    public function removing() {
      $v= new Vector();
      $o= new String('one');
      $v->add($o);
      $r= $v->remove(0);
      $this->assertEquals(0, $v->size());
      $this->assertEquals($o, $r);
    }

    /**
     * Test removing elements
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function removingPastEnd() {
      create(new Vector())->get(0);
    }

    /**
     * Test removing elements
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function removingNegative() {
      create(new Vector())->get(-1);
    }

    /**
     * Test clearing the vector
     *
     */
    #[@test]
    public function clearing() {
      $v= new Vector(array(new String('Goodbye cruel world')));
      $this->assertFalse($v->isEmpty());
      $v->clear();
      $this->assertTrue($v->isEmpty());
    }

    /**
     * Test elements()
     *
     */
    #[@test]
    public function elementsOfEmptyVector() {
      $this->assertEquals(array(), create(new Vector())->elements());
    }

    /**
     * Test elements()
     *
     */
    #[@test]
    public function elementsOf() {
      $el= array(new String('a'), new Object());
      $this->assertEquals($el, create(new Vector($el))->elements());
    }

    /**
     * Test contains()
     *
     */
    #[@test]
    public function addedStringIsContained() {
      $v= new Vector();
      $o= new String('one');
      $v->add($o);
      $this->assertTrue($v->contains($o));
    }

    /**
     * Test contains()
     *
     */
    #[@test]
    public function emptyVectorDoesNotContainString() {
      $this->assertFalse(create(new Vector())->contains(new Object()));
    }

    /**
     * Test indexOf()
     *
     */
    #[@test]
    public function indexOfOnEmptyVector() {
      $this->assertFalse(create(new Vector())->indexOf(new Object()));
    }

    /**
     * Test indexOf()
     *
     */
    #[@test]
    public function indexOf() {
      $a= new String('A');
      $this->assertEquals(0, create(new Vector(array($a)))->indexOf($a));
    }

    /**
     * Test indexOf()
     *
     */
    #[@test]
    public function indexOfElementContainedTwice() {
      $a= new String('A');
      $this->assertEquals(0, create(new Vector(array($a, new Object(), $a)))->indexOf($a));
    }

    /**
     * Test lastIndexOf()
     *
     */
    #[@test]
    public function lastIndexOfOnEmptyVector() {
      $this->assertFalse(create(new Vector())->lastIndexOf(new Object()));
    }

    /**
     * Test lastIndexOf()
     *
     */
    #[@test]
    public function lastIndexOf() {
      $a= new String('A');
      $this->assertEquals(0, create(new Vector(array($a)))->lastIndexOf($a));
    }

    /**
     * Test lastIndexOf()
     *
     */
    #[@test]
    public function lastIndexOfElementContainedTwice() {
      $a= new String('A');
      $this->assertEquals(2, create(new Vector(array($a, new Object(), $a)))->lastIndexOf($a));
    }

    /**
     * Test toString()
     *
     */
    #[@test]
    public function stringOfEmptyVector() {
      $this->assertEquals(
        "util.collections.Vector[0]@{\n}",
        create(new Vector())->toString()
      );
    }

    /**
     * Test toString()
     *
     */
    #[@test]
    public function stringOf() {
      $this->assertEquals(
        "util.collections.Vector[2]@{\n  0: One\n  1: Two\n}",
        create(new Vector(array(new String('One'), new String('Two'))))->toString()
      );
    }

    /**
     * Test iteration
     *
     */
    #[@test]
    public function iteration() {
      $v= new Vector();
      for ($i= 0; $i < 5; $i++) {
        $v->add(new String('#'.$i));
      }
      
      $i= 0;
      foreach ($v as $offset => $string) {
        $this->assertEquals($offset, $i);
        $this->assertEquals(new String('#'.$i), $string);
        $i++;
      }
    }

    /**
     * Test equals()
     *
     */
    #[@test]
    public function twoEmptyVectorsAreEqual() {
      $this->assertTrue(create(new Vector())->equals(new Vector()));
    }

    /**
     * Test equals()
     *
     */
    #[@test]
    public function sameVectorsAreEqual() {
      $a= new Vector(array(new String('One'), new String('Two')));
      $this->assertTrue($a->equals($a));
    }

    /**
     * Test equals()
     *
     */
    #[@test]
    public function vectorsWithSameContentsAreEqual() {
      $a= new Vector(array(new String('One'), new String('Two')));
      $b= new Vector(array(new String('One'), new String('Two')));
      $this->assertTrue($a->equals($b));
    }

    /**
     * Test equals() does not choke on NULL values
     *
     */
    #[@test]
    public function aVectorIsNotEqualToNull() {
      $this->assertFalse(create(new Vector())->equals(NULL));
    }

    /**
     * Test equals()
     *
     */
    #[@test]
    public function twoVectorsOfDifferentSizeAreNotEqual() {
      $this->assertFalse(create(new Vector(array(new Object())))->equals(new Vector()));
    }

    /**
     * Test equals()
     *
     */
    #[@test]
    public function orderMattersForEquality() {
      $a= array(new String('a'), new String('b'));
      $b= array(new String('b'), new String('a'));
      $this->assertFalse(create(new Vector($a))->equals(new Vector($b)));
    }
  }
?>
