<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'net.xp_framework.unittest.core.generics.Nullable'
  );

  /**
   * TestCase for generic behaviour at runtime.
   *
   * @see   xp://net.xp_framework.unittest.core.generics.Nullable
   */
  class OptionalArgTest extends TestCase {
  
    /**
     * Test constructor
     *
     */
    #[@test]
    public function create_with_value() {
      $this->assertEquals($this, create('new net.xp_framework.unittest.core.generics.Nullable<TestCase>', $this)->get());
    }

    /**
     * Test constructor
     *
     */
    #[@test]
    public function create_with_null() {
      $this->assertFalse(create('new net.xp_framework.unittest.core.generics.Nullable<TestCase>', NULL)->hasValue());
    }

    /**
     * Test set()
     *
     */
    #[@test]
    public function set_value() {
      $this->assertEquals($this, create('new net.xp_framework.unittest.core.generics.Nullable<TestCase>', $this)->set($this)->get());
    }

    /**
     * Test set()
     *
     */
    #[@test]
    public function set_null() {
      $this->assertFalse(create('new net.xp_framework.unittest.core.generics.Nullable<TestCase>', $this)->set(NULL)->hasValue());
    }
  }
?>
