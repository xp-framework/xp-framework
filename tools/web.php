<?php
  // {{{ string scanpath(string[] path, string home)
  //     Scans a path file 
  function scanpath($paths, $home) {
    $inc= '';
    foreach ($paths as $path) {
      if (!($d= @opendir($path))) continue;
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
    }
    return $inc;
  }
  // }}}

  $webroot= getenv('DOCUMENT_ROOT').'/..';
  set_include_path(scanpath(explode(PATH_SEPARATOR, get_include_path()), $webroot));
  
  // Bootstrap 
  if (!include('lang.base.php')) {
    trigger_error('[bootstrap] Cannot determine boot class path', E_USER_ERROR);
    exit(0x3d);
  }
  uses('xp.scriptlet.Runner');
  exit(xp·scriptlet·Runner::main(array($webroot)));
?>
