<?php 
  define('EPREPEND_IDENTIFIER', "\6100");

  // {{{ internal string __output(string buf)
  //     Output handler. Checks for fatal errors
  function __output($buf) {
    if (FALSE !== ($p= strpos($buf, EPREPEND_IDENTIFIER))) {
      $e= new Error(str_replace(EPREPEND_IDENTIFIER, '', substr($buf, $p)));
      fputs(STDERR, $e->toString());
    }

    return $buf;
  }
  // }}}

  // {{{ internal void __except(Exception e)
  //     Exception handler
  function __except($e) {
    fputs(STDERR, 'Uncaught exception: '.xp::stringOf($e));
    exit(0xff);
  }    
  // }}}

  // Verify SAPI
  if ('cgi' === PHP_SAPI || 'cgi-fcgi' === PHP_SAPI) {
    ini_set('html_errors', 0);
    define('STDIN', fopen('php://stdin', 'rb'));
    define('STDOUT', fopen('php://stdout', 'wb'));
    define('STDERR', fopen('php://stderr', 'wb'));
  } else if ('cli' !== PHP_SAPI) {
    trigger_error('[bootstrap] Cannot be run under '.PHP_SAPI.' SAPI', E_USER_ERROR);
    exit(0x3d);
  }
  
  if (!include(__DIR__.DIRECTORY_SEPARATOR.'lang.base.php')) {
    trigger_error('[bootstrap] Cannot determine boot class path', E_USER_ERROR);
    exit(0x3d);
  }

  $home= getenv('HOME');
  list($use, $include)= explode(PATH_SEPARATOR.PATH_SEPARATOR, get_include_path());
  bootstrap(
    scanpath(explode(PATH_SEPARATOR, substr($use, 2).PATH_SEPARATOR.'.'), $home).
    $include
  );
  uses('util.cmd.ParamString', 'util.cmd.Console');
  
  ini_set('error_prepend_string', EPREPEND_IDENTIFIER);
  set_exception_handler('__except');
  ob_start('__output');

  array_shift($_SERVER['argv']);
  try {
    exit(XPClass::forName($argv[1])->getMethod('main')->invoke(NULL, array(array_slice($argv, 2)))); 
  } catch (SystemExit $e) {
    if ($message= $e->getMessage()) echo $message, "\n";
    exit($e->getCode());
  }
?>
