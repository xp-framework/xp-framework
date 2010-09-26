<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'xp.compiler.syntax.php';

  uses('xp.compiler.Syntax', 'xp.compiler.syntax.php.Parser', 'xp.compiler.syntax.php.Lexer');

  /**
   * PHP Syntax
   *
   * @purpose  Syntax implementation
   */
  class xp·compiler·syntax·php·Syntax extends Syntax {

    /**
     * Creates a parser instance
     *
     * @return  text.parser.generic.AbstractParser
     */
    protected function newParser() {
      return new xp·compiler·syntax·php·Parser();
    }

    /**
     * Creates a lexer instance
     *
     * @param   io.streams.InputStream in
     * @param   string source
     * @return  text.parser.generic.AbstractLexer
     */
    protected function newLexer(InputStream $in, $source) {
      return new xp·compiler·syntax·php·Lexer($in, $source);
    }
  }
?>
