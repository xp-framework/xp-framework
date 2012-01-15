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
  class GenericMethodReflectionTest extends TestCase {
    protected $fixture= NULL;
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->fixture= XPClass::forName('net.xp_framework.unittest.core.GenericSerializer');
    }

    /**
     * Test
     *
     */
    #[@test]
    public function hasValueOfMethod() {
      $this->assertTrue($this->fixture->hasMethod('valueOf'));
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function valueOfIsGeneric() {
      $this->assertTrue($this->fixture->getMethod('valueOf')->isGeneric());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function valueOfName() {
      $this->assertEquals('valueOf<T>', $this->fixture->getMethod('valueOf')->getName());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function valueOfsTypeParameterIsHidden() {
      $this->assertEquals('input', $this->fixture->getMethod('valueOf')->getParameter(0)->getName());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function valueOfsTypeParameterIsHiddenFromNumParameters() {
      $this->assertEquals(1, $this->fixture->getMethod('valueOf')->numParameters());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function valueOfsTypeParameterIsHiddenFromNumParameterList() {
      $list= $this->fixture->getMethod('valueOf')->getParameters();
      $this->assertEquals(1, sizeof($list));
      $this->assertEquals('input', $list[0]->getName());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function valueOfGenericComponents() {
      $this->assertEquals(array('T'), $this->fixture->getMethod('valueOf')->genericComponents());
    }
  }
?>
