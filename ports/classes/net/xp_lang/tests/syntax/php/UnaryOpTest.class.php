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
  class net·xp_lang·tests·syntax·php·UnaryOpTest extends net·xp_lang·tests·syntax·php·ParserTestCase {
  
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
