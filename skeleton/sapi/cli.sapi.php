<?php
/* This file provides the CLI sapi for the XP framework
 * 
 * $Id$
 */

  uses('util.cmd.ParamString', 'util.cmd.Console');
  
  define('EPREPEND_IDENTIFIER', "\6100");
  
  // {{{ final class sapi·cli
  class sapi·cli {

    // {{{ internal string output(string buf)
    //     Output handler
    function output($buf) {

      // Check for fatal errors
      if (FALSE !== ($p= strpos($buf, EPREPEND_IDENTIFIER))) {
        $e= &new Error(str_replace(EPREPEND_IDENTIFIER, '', substr($buf, $p)));
        return 'Uncaught error: '.$e->toString();
      }

      // Check for uncaught exceptions
      if ($exceptions= &xp::registry('exceptions')) {
        return 'Uncaught exception: '.$exceptions[key($exceptions)]->toString();
      }

      return $buf;
    }
    // }}}
    
  }
  // }}}
  
  ob_start(array('sapi·cli', 'output'));
?>
