<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'xp.compiler.syntax.xp';

  uses('xp.compiler.Syntax', 'xp.compiler.syntax.xp.Parser', 'xp.compiler.syntax.xp.Lexer');

  /**
   * XP Language Syntax
   *
   * @purpose  Syntax implementation
   */
  class xp·compiler·syntax·xp·Syntax extends Syntax {

    /**
     * Creates a parser instance
     *
     * @return  text.parser.generic.AbstractParser
     */
    protected function newParser() {
      return new xp·compiler·syntax·xp·Parser();
    }

    /**
     * Creates a lexer instance
     *
     * @param   io.streams.InputStream in
     * @param   string source
     * @return  text.parser.generic.AbstractLexer
     */
    protected function newLexer(InputStream $in, $source) {
      return new xp·compiler·syntax·xp·Lexer($in, $source);
    }
  }
?>
