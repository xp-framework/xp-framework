<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase', 'lang.Runnable');

  /**
   * Tests cast() functionality
   *
   * @purpose  Unittest
   */
  class CastingTest extends TestCase implements Runnable {

    /**
     * Runnable implementation
     *
     */
    public function run() { 
      // Intentionally empty
    }

    /**
     * Test casting of anonymous classes created w/ newinstance()
     *
     */
    #[@test]
    public function newinstance() {
      $runnable= newinstance('lang.Runnable', array(), '{
        public function run() { return "RUN"; }
      }');
      $this->assertEquals('RUN', cast($runnable, 'lang.Runnable')->run());
    }

    /**
     * Test casting of NULL values
     *
     */
    #[@test]
    public function null() {
      $this->assertEquals(xp::null(), cast(NULL, 'lang.Object'));
    }

    /**
     * Test casting to this class
     *
     */
    #[@test]
    public function thisClass() {
      $this->assertTrue($this === cast($this, $this->getClassName()));
    }

    /**
     * Test casting to interface implemented by this class
     *
     */
    #[@test]
    public function runnableInterface() {
      $this->assertTrue($this === cast($this, 'lang.Runnable'));
    }

    /**
     * Test casting to parent class
     *
     */
    #[@test]
    public function parentClass() {
      $this->assertTrue($this === cast($this, 'unittest.TestCase'));
    }

    /**
     * Test casting to lang.Object class
     *
     */
    #[@test]
    public function objectClass() {
      $this->assertTrue($this === cast($this, 'lang.Object'));
    }

    /**
     * Test casting to interface implemented by parent class
     *
     */
    #[@test]
    public function genericInterface() {
      $this->assertTrue($this === cast($this, 'lang.Generic'));
    }

    /**
     * Test casting to unrelated class
     *
     */
    #[@test, @expect('lang.ClassCastException')]
    public function unrelated() {
      cast($this, 'lang.types.String');
    }

    /**
     * Test casting to subclass
     *
     */
    #[@test, @expect('lang.ClassCastException')]
    public function subClass() {
      cast(new Object(), 'lang.types.String');
    }

    /**
     * Test casting to a non-existant class
     *
     */
    #[@test, @expect('lang.ClassNotFoundException')]
    public function nonExistant() {
      cast($this, '@@NON_EXISTANT_CLASS@@');
    }

    /**
     * Test casting of NULL values
     *
     */
    #[@test, @expect('lang.NullPointerException')]
    public function npe() {
      cast(NULL, 'lang.Runnable')->run();
    }

    /**
     * Test casting of primitives
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function primitive() {
      cast('primitive', 'lang.Object');
    }
  }
?>
