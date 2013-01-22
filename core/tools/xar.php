<?php 
  define('EPREPEND_IDENTIFIER', "\6100");

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
            $base= $home.DIRECTORY_SEPARATOR; $line= substr($line, 1);
          } else if ('/' === $line{0} || (':' === $line{1} && '\\' === $line{2})) {
            $base= '';
          } else {
            $base= $path.DIRECTORY_SEPARATOR; 
          }

          $qn= $base.strtr(trim($line), '/', DIRECTORY_SEPARATOR).PATH_SEPARATOR;
          if ('.php' === substr($qn, -5, 4)) {
            require(substr($qn, 0, -1));
          } else {
            $pre ? $inc= $qn.$inc : $inc.= $qn;
          }
        }
      }
      closedir($d);
    }
    return $inc;
  }
  // }}}

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

  $home= getenv('HOME');
  list($use, $include)= explode(PATH_SEPARATOR.PATH_SEPARATOR, get_include_path());
  set_include_path(
    scanpath(explode(PATH_SEPARATOR, substr($use, 2).PATH_SEPARATOR.'.'), $home).
    $include
  );

  if (!include('lang.base.php')) {
    trigger_error('[bootstrap] Cannot determine boot class path', E_USER_ERROR);
    exit(0x3d);
  }
  uses('util.cmd.ParamString', 'util.cmd.Console', 'util.Properties');

  ini_set('error_prepend_string', EPREPEND_IDENTIFIER);
  set_exception_handler('__except');
  ob_start('__output');

  $cl= ClassLoader::registerLoader(new ArchiveClassLoader($argv[1]));
  $pr= Properties::fromString($cl->getResource('META-INF/manifest.ini'));

  array_shift($_SERVER['argv']);
  exit(XPClass::forName($pr->readString('archive', 'main-class'), $cl)->getMethod('main')->invoke(NULL, array(array_slice($argv, 2)))); 
?>
