<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_lang.tests.syntax.xp.ParserTestCase');

  /**
   * TestCase
   *
   */
  class ArrayTest extends ParserTestCase {

    /**
     * Test an empty untyped array
     *
     */
    #[@test]
    public function emptyUntypedArray() {
      $this->assertEquals(array(new ArrayNode(array(
        'values'        => NULL,
        'type'          => NULL,
      ))), $this->parse('
        [];
      '));
    }

    /**
     * Test an empty typed array
     *
     */
    #[@test]
    public function emptyTypedArray() {
      $this->assertEquals(array(new ArrayNode(array(
        'values'        => NULL,
        'type'          => new TypeName('int[]'),
      ))), $this->parse('
        new int[] {};
      '));
    }

    /**
     * Test a non-empty untyped array
     *
     */
    #[@test]
    public function untypedArray() {
      $this->assertEquals(array(new ArrayNode(array(
        'values'        => array(
          new IntegerNode('1'),
          new IntegerNode('2'),
          new IntegerNode('3'),
        ),
        'type'          => NULL,
      ))), $this->parse('
        [1, 2, 3];
      '));
    }

    /**
     * Test a non-empty untyped array
     *
     */
    #[@test]
    public function untypedArrayWithDanglingComma() {
      $this->assertEquals(array(new ArrayNode(array(
        'values'        => array(
          new IntegerNode('1'),
          new IntegerNode('2'),
          new IntegerNode('3'),
        ),
        'type'          => NULL,
      ))), $this->parse('
        [1, 2, 3, ];
      '));
    }

    /**
     * Test a non-empty typed array
     *
     */
    #[@test]
    public function typedArray() {
      $this->assertEquals(array(new ArrayNode(array(
        'values'        => array(
          new IntegerNode('1'),
          new IntegerNode('2'),
          new IntegerNode('3'),
        ),
        'type'          => new TypeName('int[]'),
      ))), $this->parse('
        new int[] {1, 2, 3};
      '));
    }
  }
?>
