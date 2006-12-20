<?php
/* This file is part of the XP framework's examples
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('util.Calendar');

  // {{{ string underline(string str [, char character = '-'])
  //     Returns the given string underlined by the specified character
  function underline($str, $character= '-') {
    return $str."\n".str_repeat($character, strlen($str));
  }
  // }}}

  // {{{ main
  $db_eu= Calendar::dstBegin();
  $db_us= Calendar::dstBegin(-1, CAL_DST_US);
  $de= Calendar::dstEnd();

  Console::writeLine(underline('Daylight savings time'));
  Console::writeLine('  Begin (EU) : ', $db_eu->format('%c'));
  Console::writeLine('  Begin (US) : ', $db_us->format('%c'));
  Console::writeLine('  End        : ', $de->format('%c'));
  // }}}
?>
