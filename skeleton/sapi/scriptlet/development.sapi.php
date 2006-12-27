<?php
/* This file provides the development scriptlet sapi for the XP framework
 * 
 * $Id$
 */

  uses('sapi.scriptlet.ScriptletRunner');

  // {{{ final class scriptlet
  class scriptlet {
  
    // {{{ void run(&scriptlet.HttpScriptlet scriptlet)
    //     Runs a scriptlet and prints XML tree for debug
    public static function run($scriptlet) {
      $runner= new ScriptletRunner(SCRIPTLET_SHOW_STACKTRACE|SCRIPTLET_SHOW_XML|SCRIPTLET_SHOW_ERRORS);
      $runner->run($scriptlet);
    }
    // }}}
  }
  // }}}
?>
