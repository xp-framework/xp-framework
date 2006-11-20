<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase', 'BaseClass', 'ChildClass', 'This');

  /**
   * Tests "this"
   *
   * @purpose  TestCase
   */
  class ThisTestCase extends TestCase {
  
    /**
     * Tests BaseClass::staticMethod()
     *
     * @access  public
     */
    #[@test]
    function nameOfBaseClass() {
      $this->assertEquals('base', BaseClass::staticMethod());
    }

    /**
     * Tests ChildClass::staticMethod()
     *
     * @access  public
     */
    #[@test]
    function nameOfChildClass() {
      $this->assertEquals('child', ChildClass::staticMethod());
    }
  }
?>
