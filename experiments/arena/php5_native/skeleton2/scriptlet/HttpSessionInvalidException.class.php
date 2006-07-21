<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('scriptlet.HttpScriptletException');

  /**
   * Indicates the session is invalid
   *
   * @see      xp://scriptlet.HttpScriptletException
   * @purpose  Exception
   */  
  class HttpSessionInvalidException extends HttpScriptletException {
  }
?>
