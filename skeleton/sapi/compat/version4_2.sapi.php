<?php
/* This file provides the PHP 4.2 compatibility sapi for the XP framework
 * 
 * $Id$
 */

  if (!defined('STDOUT')) {
    define('STDOUT', fopen('php://stdout', 'wb'));
    define('STDERR', fopen('php://stderr', 'wb'));
    define('STDIN',  fopen('php://stdin',  'rb'));
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
  if (!function_exists('debug_backtrace')) { function debug_backtrace() {
    return array();
  }}
  // }}}

  // {{{ proto array file_get_contents(string filename)
  //     See php://file_get_contents
  if (!function_exists('file_get_contents')) { function file_get_contents($filename) {
    if (!$fd= fopen($filename, 'rb')) return FALSE;
    $buf= fread($fd, filesize($filename));
    fclose($fd);
    return $buf;
  }}
  // }}}
?>
