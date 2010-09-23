<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'net.xp_lang.tests.syntax.php';

  uses('net.xp_lang.tests.syntax.php.LexerTest', 'io.streams.MemoryInputStream');

  /**
   * Tests the lexer tokenizing a stream
   *
   */
  class net·xp_lang·tests·syntax·php·StreamLexerTest extends net·xp_lang·tests·syntax·php·LexerTest {

    /**
     * Creates a lexer instance
     *
     * @param   string in
     * @return  xp.compiler.syntax.php.Lexer
     */
    protected function newLexer($in) {
      return new xp·compiler·syntax·php·Lexer(new MemoryInputStream($in), $this->name);
    }
  }
?>
