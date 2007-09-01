<?php
/* This class is part of the XP framework
 *
 * $Id: HttpSessionInvalidException.class.php 3151 2004-03-09 21:21:14Z friebe $
 */

  namespace scriptlet;

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
