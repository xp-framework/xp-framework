<?php
/* This file is part of the XP framework
 *
 * $Id$ 
 */

  function fn2xp($filename) {
    return strtr(realpath(chop($filename)), array(
      SKELETON_PATH         => '',
      DIRECTORY_SEPARATOR   => '.',
      '.class.php'          => '',
    ));
  }

  // {{{ main
  if (isset($_SERVER['argv'][1])) {
  
    // Filename specified
    require($_SERVER['argv'][1]);
    $default= array(realpath($_SERVER['argv'][1]) => 0);
  } else {
  
    // Read filenames from STDIN
    require('lang.base.php');
    $default= array_flip(get_required_files());
    
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

