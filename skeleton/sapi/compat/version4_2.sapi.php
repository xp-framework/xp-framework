<?php
/* This file provides the PHP 4.2 compatibility sapi for the XP framework
 * 
 * $Id$
 */

  if (!defined('PATH_SEPARATOR')) {
    define('PATH_SEPARATOR',  'WIN' == substr(PHP_OS, 0, 3) ? ';' : ':');    
  }

  // {{{ proto array sybase_fetch_assoc(resource result)
  //     See php://sybase_fetch_assoc
  if (!function_exists('sybase_fetch_assoc')) { function sybase_fetch_assoc($res) {
    if (is_array($r= sybase_fetch_array($res))) foreach (array_keys($r) as $k) {
      if (is_int($k)) unset($r[$k]);
    }
    return $r;
  }}
  // }}}

  // {{{ proto array debug_backtrace(void)
  //     See php://debug_backtrace
  if (!function_exists('debug_backtrace')) { function debug_backtrace($res) {
    return array();
  }}
  // }}}
?>
