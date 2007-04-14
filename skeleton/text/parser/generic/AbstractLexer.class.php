<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Lexer
   *
   * @see      xp://text.parser.generic.AbstractParser
   * @purpose  Abstract base class
   */
  abstract class AbstractLexer extends Object {
    public
      $token    = NULL,
      $value    = NULL,
      $position = array();

    /**
     * Advance to next token. Return TRUE and set token, value and
     * position members to indicate we have more tokens, or FALSE
     * to indicate we've arrived at the end of the tokens.
     *
     * @return  bool
     */
    public abstract function advance();
  }
?>
