<?php
  $webroot= getenv('WEB_ROOT') ?: $_SERVER['DOCUMENT_ROOT'].'/..';
  $configd= ini_get('user_dir') ?: $webroot.'/etc';

  // Set error status to 500 by default - if a fatal error occurs,
  // this guarantees to at least send an error code.
  switch (php_sapi_name()) {
    case 'cgi':
      header('Status: 500 Internal Server Error');
      break;

    case 'cli-server':
      if (is_file($_SERVER['DOCUMENT_ROOT'].$_SERVER['REQUEST_URI'])) {
        return FALSE;
      }

      header('HTTP/1.0 500 Internal Server Error');
      $_SERVER['SCRIPT_URL']= substr($_SERVER['REQUEST_URI'], 0, strcspn($_SERVER['REQUEST_URI'], '?#'));
      $_SERVER['SERVER_PROFILE']= getenv('SERVER_PROFILE');
      define('STDIN', fopen('php://stdin', 'rb'));
      define('STDOUT', fopen('php://stdout', 'wb'));
      define('STDERR', fopen('php://stderr', 'wb'));
      break;

    default:
      header('HTTP/1.0 500 Internal Server Error');
  }
  ini_set('error_prepend_string', '<xmp>');
  ini_set('error_append_string', '</xmp>');
  ini_set('html_errors', 0);

  // Bootstrap 
  if (!include(__DIR__.DIRECTORY_SEPARATOR.'lang.base.php')) {
    trigger_error('[bootstrap] Cannot determine boot class path', E_USER_ERROR);
    exit(0x3d);
  }

  // Set up class path
  $paths= array();
  list($use, $include)= explode(PATH_SEPARATOR.PATH_SEPARATOR, get_include_path());
  foreach (explode(PATH_SEPARATOR, $use) as $path) {
    $paths[]= ('~' == $path{0}
      ? str_replace('~', $webroot, $path)
      : $path
    );
  }
  bootstrap(scanpath($paths, $webroot).$include);
  
  uses('xp.scriptlet.Runner');
  exit(xp·scriptlet·Runner::main(array($webroot, $configd, $_SERVER['SERVER_PROFILE'], $_SERVER['SCRIPT_URL'])));
?>
