<?php
/* This file is part of the XP framework
 *
 * $Id$ 
 */

  require('lang.base.php');
  
  function fn2xp($filename) {
    return strtr(realpath(chop($filename)), array(
      SKELETON_PATH         => '',
      DIRECTORY_SEPARATOR   => '.',
      '.class.php'          => '',
    ));
  }

  // {{{ main
  $default= array_flip(get_required_files());
  
  // Read filenames from STDIN
  try(); {
    $fd= fopen('php://stdin', 'r');
    while ($line= fgets($fd, 4096)) {
      ClassLoader::loadClass(fn2xp($line));
    }
    fclose($fd);
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit();
  }
  
  // Output required files
  $str= '';
  foreach (get_required_files() as $file) {
    if (isset($default[$file])) continue;
    $str.= '|xp://'.fn2xp($file);
  }
  
  echo 'files="'.substr($str, 1).'"';
  // }}}
?>

