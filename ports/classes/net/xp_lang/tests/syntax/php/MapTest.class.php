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
  class net·xp_lang·tests·syntax·php·MapTest extends net·xp_lang·tests·syntax·php·ParserTestCase {
  
    /**
     * Test a non-empty untyped map
     *
     */
    #[@test]
    public function untypedMap() {
      $this->assertEquals(
        array(new MapNode(array(
          'elements'      => array(
            array(
              new IntegerNode('1'),
              new StringNode('one'),
            ),
            array(
              new IntegerNode('2'),
              new StringNode('two'),
            ),
            array(
              new IntegerNode('3'),
              new StringNode('three'),
            ),
          ),
          'type'          => NULL,
        ))), 
        $this->parse('array(1 => "one", 2 => "two", 3 => "three");')
      );
    }

    /**
     * Test a non-empty untyped map
     *
     */
    #[@test]
    public function untypedMapWithDanglingComma() {
      $this->assertEquals(
        array(new MapNode(array(
          'elements'      => array(
            array(
              new IntegerNode('1'),
              new StringNode('one'),
            ),
            array(
              new IntegerNode('2'),
              new StringNode('two'),
            ),
            array(
              new IntegerNode('3'),
              new StringNode('three'),
            ),
          ),
          'type'          => NULL,
        ))), 
        $this->parse('array(1 => "one", 2 => "two", 3 => "three", );')
      );
    }
  }
?>
