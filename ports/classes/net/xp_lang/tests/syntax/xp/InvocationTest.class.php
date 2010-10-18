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
  class InvocationTest extends ParserTestCase {

    /**
     * Test writeLine()
     *
     */
    #[@test]
    public function writeLine() {
      $this->assertEquals(
        array(new InvocationNode('writeLine', array(new VariableNode('m')))),
        $this->parse('writeLine($m);')
      );
    }
  }
?>
