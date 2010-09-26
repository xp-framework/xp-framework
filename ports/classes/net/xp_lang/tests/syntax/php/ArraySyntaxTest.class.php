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
  class net·xp_lang·tests·syntax·php·ArraySyntaxTest extends net·xp_lang·tests·syntax·php·ParserTestCase {
  
    /**
     * Test [1]
     *
     */
    #[@test]
    public function integerOffset() {
      $this->assertEquals(
        array(new ArrayAccessNode(new VariableNode('b'), new IntegerNode('1'))),
        $this->parse('$b[1];')
      );
    }

    /**
     * Test ["a"]
     *
     */
    #[@test]
    public function stringOffset() {
      $this->assertEquals(
        array(new ArrayAccessNode(new VariableNode('b'), new StringNode('a'))),
        $this->parse('$b["a"];')
      );
    }

    /**
     * Test []
     *
     */
    #[@test]
    public function noOffset() {
      $this->assertEquals(
        array(new ArrayAccessNode(new VariableNode('b'), NULL)),
        $this->parse('$b[];')
      );
    }

    /**
     * Test $str{$i}
     *
     */
    #[@test]
    public function curlyBraces() {
      $this->assertEquals(
        array(new ArrayAccessNode(new VariableNode('str'), new VariableNode('i'))),
        $this->parse('$str{$i};')
      );
    }
  }
?>
