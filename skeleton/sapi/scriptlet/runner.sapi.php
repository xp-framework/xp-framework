<?php
/* This file provides the base of the scriptlet sapi for the XP framework
 * 
 * $Id$
 */

  // {{{ abstract class scriptlet·runner
  class scriptlet·runner {
  
    // {{{ void run(&scriptlet.HttpScriptlet scriptlet)
    //     Runs a scriptlet.
    function run(&$scriptlet) {
      try(); {
        $scriptlet->init();
        $response= &$scriptlet->process();
      } if (catch('HttpScriptletException', $e)) {
        $response= &$e->getResponse();
        scriptlet::except($response, $e);
      }

      // Send output
      $response->sendHeaders();
      $response->sendContent();
      flush();

      // Call scriptlet's finalizer
      $scriptlet->finalize();
    }
    // }}}
    
  }
  // }}}
?>
