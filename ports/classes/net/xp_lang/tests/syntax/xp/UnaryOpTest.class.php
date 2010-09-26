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
  class UnaryOpTest extends ParserTestCase {
  
    /**
     * Test negation
     *
     */
    #[@test]
    public function negation() {
      $this->assertEquals(array(new UnaryOpNode(array(
        'expression'    => new VariableNode('i'),
        'op'            => '!'
      ))), $this->parse('
        !$i;
      '));
    }

    /**
     * Test complement
     *
     */
    #[@test]
    public function complement() {
      $this->assertEquals(array(new UnaryOpNode(array(
        'expression'    => new VariableNode('i'),
        'op'            => '~'
      ))), $this->parse('
        ~$i;
      '));
    }

    /**
     * Test increment
     *
     */
    #[@test]
    public function increment() {
      $this->assertEquals(array(new UnaryOpNode(array(
        'expression'    => new VariableNode('i'),
        'op'            => '++'
      ))), $this->parse('
        ++$i;
      '));
    }

    /**
     * Test decrement
     *
     */
    #[@test]
    public function decrement() {
      $this->assertEquals(array(new UnaryOpNode(array(
        'expression'    => new VariableNode('i'),
        'op'            => '--'
      ))), $this->parse('
        --$i;
      '));
    }
  }
?>
