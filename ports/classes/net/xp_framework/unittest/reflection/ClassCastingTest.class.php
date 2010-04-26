<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestCase');

  /**
   * TestCase
   *
   * @see      xp://lang.XPClass#cast
   */
  class ClassCastingTest extends TestCase {

    /**
     * Tests cast() method
     *
     */
    #[@test]
    public function thisClassCastingThis() {
      $this->assertEquals($this, $this->getClass()->cast($this));
    }

    /**
     * Tests cast() method
     *
     */
    #[@test]
    public function parentClassCastingThis() {
      $this->assertEquals($this, $this->getClass()->getParentClass()->cast($this));
    }

    /**
     * Tests cast() method
     *
     */
    #[@test]
    public function objectClassCastingThis() {
      $this->assertEquals($this, XPClass::forName('lang.Object')->cast($this));
    }

    /**
     * Tests cast() method
     *
     */
    #[@test, @expect('lang.ClassCastException')]
    public function thisClassCastingAnObject() {
      $this->getClass()->cast(new Object());
    }

    /**
     * Tests cast() method
     *
     */
    #[@test, @expect('lang.ClassCastException')]
    public function thisClassCastingAnUnrelatedClass() {
      $this->getClass()->cast(new String('Hello'));
    }

    /**
     * Tests cast() method
     *
     */
    #[@test]
    public function thisClassCastingNull() {
      $this->assertEquals(xp::null(), $this->getClass()->cast(NULL));
    }

    /**
     * Tests cast() method
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function castPrimitive() {
      $this->getClass()->cast(0);
    }
  }
?>
