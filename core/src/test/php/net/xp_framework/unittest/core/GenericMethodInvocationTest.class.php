<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'net.xp_framework.unittest.core.GenericSerializer'
  );

  /**
   * TestCase
   *
   * @see   xp://net.xp_framework.unittest.core.GenericSerializer
   */
  class GenericMethodInvocationTest extends TestCase {
    protected $fixture= NULL;
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->fixture= new GenericSerializer();
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function valueOfInt() {
      $this->assertEquals(1, $this->fixture->{'valueOf<int>'}('i:1;'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function valueOfString() {
      $this->assertEquals('Test', $this->fixture->{'valueOf<string>'}('s:4:"Test";'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function valueOfVar() {
      $this->assertEquals('Test', $this->fixture->{'valueOf<var>'}('s:4:"Test";'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function valueOfNullAsObject() {
      $this->assertNull($this->fixture->{'valueOf<Object>'}('N;'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function valueOfNullAsVar() {
      $this->assertNull($this->fixture->{'valueOf<var>'}('N;'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function valueOfNullAsInt() {
      $this->assertEquals(0, $this->fixture->{'valueOf<int>'}('N;'));
    }
  }
?>
