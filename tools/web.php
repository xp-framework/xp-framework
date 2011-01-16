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
          } else if ('!' === $line{0}) {
            $pre= TRUE;
            $line= substr($line, 1);
          } else {
            $pre= FALSE;
          }
          
          if ('~' === $line{0}) {
            $base= $home; $line= substr($line, 1);
          } else if ('/' === $line{0} || (':' === $line{1} && '\\' === $line{2})) {
            $base= '';
          } else {
            $base= $path; 
          }

          $qn= $base.DIRECTORY_SEPARATOR.strtr(trim($line), '/', DIRECTORY_SEPARATOR).PATH_SEPARATOR;
          $pre ? $inc= $qn.$inc : $inc.= $qn;
        }
      }
      closedir($d);
    }
    return $inc;
  }
  // }}}

  // Set error status to 500 by default - if a fatal error occurs,
  // this guarantees to at least send an error code.
  switch (php_sapi_name()) {
    case 'cgi':
      header('Status: 500 Internal Server Error');
      break;

    default:
      header('HTTP/1.0 500 Internal Server Error');
  }
  ini_set('error_prepend_string', '<xmp>');
  ini_set('error_append_string', '</xmp>');
  ini_set('html_errors', 0);

  $webroot= getenv('DOCUMENT_ROOT').'/..';
  $paths= array();
  foreach (explode(PATH_SEPARATOR, get_include_path()) as $path) {
    $paths[]= ('~' == $path{0}
      ? str_replace('~', $webroot, $path)
      : $path
    );
  }
  set_include_path(rtrim(scanpath($paths, $webroot), PATH_SEPARATOR));
  
  // Bootstrap 
  if (!include('lang.base.php')) {
    trigger_error('[bootstrap] Cannot determine boot class path', E_USER_ERROR);
    exit(0x3d);
  }
  uses('xp.scriptlet.Runner');
  exit(xp·scriptlet·Runner::main(array($webroot, getenv('SERVER_PROFILE'), getenv('SCRIPT_URL'))));
?>
