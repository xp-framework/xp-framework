<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestCase');

  /**
   * Tests typeof() functionality
   *
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

    /**
     * Test typeof(array)
     *
     */
    #[@test]
    public function intArray() {
      $this->assertEquals(ArrayType::forName('var[]'), typeof(array(1, 2, 3)));
    }

    /**
     * Test typeof(map)
     *
     */
    #[@test]
    public function intMap() {
      $this->assertEquals(MapType::forName('[:var]'), typeof(array('one' => 1, 'two' => 2, 'three' => 3)));
    }
  }
?>
