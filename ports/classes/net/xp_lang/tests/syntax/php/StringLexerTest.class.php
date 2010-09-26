<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'net.xp_lang.tests.syntax.php';

  uses('net.xp_lang.tests.syntax.php.LexerTest');

  /**
   * Tests the lexer tokenizing string input
   *
   */
  class net·xp_lang·tests·syntax·php·StringLexerTest extends net·xp_lang·tests·syntax·php·LexerTest {

    /**
     * Creates a lexer instance
     *
     * @param   string in
     * @return  xp.compiler.syntax.php.Lexer
     */
    protected function newLexer($in) {
      return new xp·compiler·syntax·php·Lexer($in, $this->name);
    }
  }
?>
