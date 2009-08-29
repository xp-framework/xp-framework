<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestCase');

  /**
   * Tests typeof() functionality
   *
   * @purpose  Unittest
   */
  class TypeOfTest extends TestCase {

    /**
     * Test typeof(NULL)
     *
     */
    #[@test]
    public function null() {
      $this->assertEquals(Type::$VOID, typeof(NULL));
    }

    /**
     * Test typeof($this)
     *
     */
    #[@test]
    public function this() {
      $this->assertEquals($this->getClass(), typeof($this));
    }

    /**
     * Test typeof(string)
     *
     */
    #[@test]
    public function string() {
      $this->assertEquals(Primitive::$STRING, typeof($this->name));
    }
  }
?>
