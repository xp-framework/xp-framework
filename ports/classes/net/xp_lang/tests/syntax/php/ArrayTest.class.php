<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_lang.tests.syntax.php';

  uses('net.xp_lang.tests.syntax.php.ParserTestCase');

  /**
   * TestCase
   *
   */
  class net·xp_lang·tests·syntax·php·ArrayTest extends net·xp_lang·tests·syntax·php·ParserTestCase {

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
        array();
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
        array(1, 2, 3);
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
        array(1, 2, 3, );
      '));
    }
  }
?>
