<?php
/* This file is part of the XP framework
 *
 * $Id$
 */

  uses('lang.ChainedException');
  
  /**
   * Indicates an error occured during parsing
   *
   * @see       xp://text.parser.generic.AbstractParser#parse
   * @purpose   Exception
   */
  class ParseException extends ChainedException {

  }
?>
