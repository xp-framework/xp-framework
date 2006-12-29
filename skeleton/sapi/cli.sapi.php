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
    static function output($buf) {

      // Check for fatal errors
      if (FALSE !== ($p= strpos($buf, EPREPEND_IDENTIFIER))) {
        $e= new Error(str_replace(EPREPEND_IDENTIFIER, '', substr($buf, $p)));
        fputs(STDERR, $e->toString());
      }

      return $buf;
    }
    // }}}
    
    // {{{ internal void except(Exception e)
    //     Exception handler
    static function except($e) {
      fputs(STDERR, 'Uncaught exception: '.xp::stringOf($e));
    }    
    // }}}
  }
  
  if (PHP_SAPI != 'cli') {
    xp::error('[sapi::cli] Cannot be run under '.PHP_SAPI.' SAPI');
    // Bails out
  }

  ini_set('error_prepend_string', EPREPEND_IDENTIFIER);
  set_exception_handler(array('sapi·cli', 'except'));
  ob_start(array('sapi·cli', 'output'));
?>
