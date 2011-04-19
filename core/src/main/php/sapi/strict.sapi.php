<?php
/* This file provides the strict sapi for the XP framework
 * 
 * $Id$
 */

  // {{{ internal void __estrict(int code, string msg, string file, int line)
  //     Error callback
  function __estrict($code, $msg, $file, $line) {
    if (0 == error_reporting()) return;
    
    switch ($msg) {
      case 1 == preg_match('/^Undefined (offset|variable|index)/', $msg):
      case 1 == preg_match('/^Use of undefined constant/', $msg):
      case 1 == preg_match('/to string conversion$/', $msg):
      case 1 == preg_match('/^Missing argument/', $msg):
      case 1 == preg_match('/^Illegal string offset/', $msg):
      case 1 == preg_match('/^Illegal offset type/', $msg):
        xp::error(xp::stringOf(new Error('[strict] "'.$msg.'" at '.$file.':'.$line)));
        // Bails
      
      default:        
        __error($code, $msg, $file, $line);
    }
  }
  // }}}
  
  // Kill original error handler and install ourselves
  restore_error_handler();
  set_error_handler('__estrict');
?>
