<?php
/* This file provides the CLI sapi for the XP framework
 * 
 * $Id$
 */

  uses('util.cmd.ParamString', 'util.cmd.Console');
  
  define('EPREPEND_IDENTIFIER', "\6100");
  
  if (PHP_SAPI != 'cli') {
    xp::error('[sapi::cli] Cannot be run under '.PHP_SAPI.' SAPI');
    // Bails out
  }
?>
