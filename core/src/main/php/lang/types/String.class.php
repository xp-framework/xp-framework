<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.types.Character', 'lang.types.Bytes');

  if (extension_loaded('mbstring')) {
    fputs(STDERR, "String.mbstring.partial.php\n");
    require(__DIR__.DIRECTORY_SEPARATOR.'String.mbstring.partial.php');
  } else {
    fputs(STDERR, "String.iconv.partial.php\n");
    require(__DIR__.DIRECTORY_SEPARATOR.'String.iconv.partial.php');
  }
?>
