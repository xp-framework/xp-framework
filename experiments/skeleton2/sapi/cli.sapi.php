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
        $e= new Error(str_replace(EPREPEND_IDENTIFIER, '', substr($buf, $p)));
        return 'Uncaught error: '.$e->toString();
      }

      return $buf;
    }
    // }}}

    // {{{ internal void exception(Exception e)
    //     Exception handler
    function exception($e) {
      echo 'Uncaught ', $e;
    }
    // }}}
  }
  // }}}
  
  ini_set('error_prepend_string', EPREPEND_IDENTIFIER);
  set_exception_handler(array('sapi·cli', 'exception'));
  ob_start(array('sapi·cli', 'output'));
?>
