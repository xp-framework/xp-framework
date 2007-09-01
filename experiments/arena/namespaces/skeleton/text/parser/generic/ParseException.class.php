<?php
/* This file is part of the XP framework
 *
 * $Id$
 */

  namespace text::parser::generic;

  uses('lang.ChainedException');
  
  /**
   * Indicates an error occured during parsing
   *
   * @see       xp://text.parser.generic.AbstractParser#parse
   * @purpose   Exception
   */
  class ParseException extends lang::ChainedException {

  }
?>
