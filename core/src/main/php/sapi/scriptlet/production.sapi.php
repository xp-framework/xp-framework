<?php
/* This file provides the production scriptlet sapi for the XP framework
 * 
 * $Id$
 */

  uses('sapi.scriptlet.ScriptletRunner');

  // {{{ final class scriptlet
  class scriptlet {
  
    // {{{ void run(&scriptlet.HttpScriptlet scriptlet)
    //     Runs a scriptlet and prints XML tree for production
    public static function run($scriptlet) {
      $runner= new ScriptletRunner(0x000);
      $runner->run($scriptlet);
    }
  }
  // }}}
?>
