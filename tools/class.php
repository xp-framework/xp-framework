<?php 
  // {{{ string scanpath(string path, string home, bool fatal)
  //     Scans a path file 
  function scanpath($path, $home, $fatal= FALSE) {
    if (!($d= @opendir($path))) return '';
    $inc= '';
    while ($e= readdir($d)) {
      if ('.pth' !== substr($e, -4)) continue;

      foreach (file($path.DIRECTORY_SEPARATOR.$e) as $line) {
        if ('#' === $line{0}) {
          continue;
        } else if ('~' === $line{0}) {
          $base= $home; $line= substr($line, 1);
        } else if ('/' === $line{0} || (':' === $line{1} && '\\' === $line{2})) {
          $base= '';
        } else {
          $base= $path; 
        }
        
        $inc.= $base.DIRECTORY_SEPARATOR.strtr(trim($line), '/', DIRECTORY_SEPARATOR).PATH_SEPARATOR;
      }
    }
    closedir($d);
    if ($fatal && !$inc) {
      echo '[bootstrap] Cannot determine boot class path in ', realpath($path), "\n";
      exit(0x3d);
    }
    return $inc;
  }
  // }}}

  $home= getenv('HOME');
  set_include_path(
    scanpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..', $home, TRUE).
    scanpath('.', $home).
    get_include_path()
  );

  require('lang.base.php');
  xp::sapi('cli');

  exit(XPClass::forName($argv[1])->getMethod('main')->invoke(NULL, array(array_slice($argv, 2)))); 
?>
