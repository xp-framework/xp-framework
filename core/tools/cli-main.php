<?php /*- Entry point: CLI -*/

// Verify PHP version and Server API
if (version_compare(PHP_VERSION, '5.3.0', '<')) {
  trigger_error('This version of the XP Framework requires PHP 5.3.0+, have PHP '.PHP_VERSION.PHP_EOL, E_USER_ERROR);
  exit(0x3d);
} else if ('cgi' === PHP_SAPI || 'cgi-fcgi' === PHP_SAPI) {
  ini_set('html_errors', 0);
  define('STDIN', fopen('php://stdin', 'rb'));
  define('STDOUT', fopen('php://stdout', 'wb'));
  define('STDERR', fopen('php://stderr', 'wb'));
} else if ('cli' !== PHP_SAPI) {
  trigger_error('[bootstrap] Cannot be run under '.PHP_SAPI.' SAPI'.PHP_EOL, E_USER_ERROR);
  exit(0x3d);
}

// {{{ internal string __output(string buf)
//     Output handler. Checks for fatal errors
function __output($buf) {
  if (false !== ($p= strpos($buf, EPREPEND_IDENTIFIER))) {
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

if (!include(__DIR__.DIRECTORY_SEPARATOR.'lang.base.php')) {
  trigger_error('[bootstrap] Cannot determine boot class path', E_USER_ERROR);
  exit(0x3d);
}

// Bootstrap: Set up class path
$home= getenv('HOME');
list($use, $include)= explode(PATH_SEPARATOR.PATH_SEPARATOR, get_include_path());
bootstrap(
  scanpath(explode(PATH_SEPARATOR, substr($use, 2).PATH_SEPARATOR.'.'), $home).
  $include
);
uses('util.cmd.ParamString', 'util.cmd.Console');

// Start input layer: Decode arguments to xp::ENCODING
array_shift($_SERVER['argv']);
array_shift($argv);
if (xp::ENCODING !== ($input= iconv_get_encoding('input_encoding'))) {
  foreach ($_SERVER['argv'] as $i => $val) {
    $argv[$i]= $_SERVER['argv'][$i]= iconv($input, xp::ENCODING."//IGNORE", $val);
  }
}

// Start output layer: Set up error handling and output callback
define('EPREPEND_IDENTIFIER', "\6100");
ini_set('error_prepend_string', EPREPEND_IDENTIFIER);
set_exception_handler('__except');
ob_start('__output');

// Run main()
try {
  exit(XPClass::forName($argv[0])->getMethod('main')->invoke(NULL, array(array_slice($argv, 1)))); 
} catch (SystemExit $e) {
  if ($message= $e->getMessage()) echo $message, "\n";
  exit($e->getCode());
}
