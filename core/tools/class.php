<?php
  define('EPREPEND_IDENTIFIER', "\6100");
  if (version_compare(PHP_VERSION, '5.3.0', '<')) {
    trigger_error('This version of the XP Framework requires PHP 5.3.0+, have PHP '.PHP_VERSION.PHP_EOL, E_USER_ERROR);
    exit(0x3d);
  }

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

  // Set CLI specific handling
  $home= getenv('HOME');
  $cwd= '.';

  if ('cgi' === PHP_SAPI || 'cgi-fcgi' === PHP_SAPI) {
    ini_set('html_errors', 0);
    define('STDIN', fopen('php://stdin', 'rb'));
    define('STDOUT', fopen('php://stdout', 'wb'));
    define('STDERR', fopen('php://stderr', 'wb'));
  } else if ('cli' !== PHP_SAPI) {
    trigger_error('[bootstrap] Cannot be run under '.PHP_SAPI.' SAPI', E_USER_ERROR);
    exit(0x3d);
  }
  
  function path($in, $bail= true) {
    $qn= realpath($in);
    if (false === $qn) {
      if ($bail) {
        trigger_error('[bootstrap] Classpath element ['.$in.'] not found', E_USER_ERROR);
        exit(0x3d);
      }
      return null;
    } else {
      return is_dir($qn) ? $qn.DIRECTORY_SEPARATOR : $qn;
    }
  }

  function pathfiles($path) {
    $result= array();
    if ($pr= @opendir($path)) {
      while ($file= readdir($pr)) {
        if (0 !== substr_compare($file, '.pth', -4)) continue;

        foreach (file($path.DIRECTORY_SEPARATOR.$file) as $line) {
          $line= trim($line);
          if ('' === $line || '#' === $line{0}) {
            continue;
          } else {
            $result[]= $line;
          }
        }
      }
      closedir($pr);
    }
    return $result;
  }

  function scanpath(&$result, $paths, $base, $home) {
    $type= 'local';

    if (null === $result['base']) {
      if (is_file($f= $base.DIRECTORY_SEPARATOR.'tools'.DIRECTORY_SEPARATOR.'lang.base.php')) {
        $result['base']= $f;
        $type= 'core';
      }
    }

    foreach ($paths as $path) {
      if ('' === $path) continue;

      // Handle ? and ! prefixes
      $bail= true;
      $overlay= null;
      if ('!' === $path{0}) {
        $overlay= 'overlay';
        $path= '!' === $path ? '.' : substr($path, 1);
      } else if ('?' === $path{0}) {
        $bail= false;
        $path= substr($path, 1);
      }

      // Expand file path
      if ('~' === $path{0}) {
        $expanded= $home.DIRECTORY_SEPARATOR.substr($path, 1);
      } else if ('/' === $path{0} || '\\' === $path{0} || strlen($path) > 2 && (':' === $path{1} && '\\' === $path{2})) {
        $expanded= $path;
      } else {
        $expanded= $base.DIRECTORY_SEPARATOR.$path;
      }

      // Resolve
      if ($resolved= path($expanded, $bail)) {
        if (0 === substr_compare($resolved, '.php', -4)) {
          $result['files'][]= $resolved;
        } else {
          $result[$overlay ?: $type][]= $resolved;
        }
      }
    }
  }

  function bootstrap($cwd, $home) {
    $result= array(
      'base'     => null,
      'overlay'  => array(),
      'core'     => array(),
      'local'    => array(),
      'files'    => array()
    );
    $parts= explode(PATH_SEPARATOR.PATH_SEPARATOR, get_include_path());

    // Check local module first
    scanpath($result, pathfiles($cwd), $cwd, $home);

    // We rely classpath always includes "." at the beginning
    if (isset($parts[1])) {
      foreach (explode(PATH_SEPARATOR, substr($parts[1], 2)) as $path) {
        scanpath($result, array($path), $cwd, $home);
      }
    }

    // We rely modules always includes "." at the beginning
    foreach (array_unique(explode(PATH_SEPARATOR, substr($parts[0], 2))) as $path) {
      if ('' === $path) {
        continue;
      } else if ('~' === $path{0}) {
        $path= $home.substr($path, 1);
      }
      scanpath($result, pathfiles($path), $path, $home);
    }

    // Always add current directory
    $result['local'][]= path($cwd);
    return $result;
  }

  $bootstrap= bootstrap($cwd, $home);
  foreach ($bootstrap['files'] as $file) {
    require $file;
  }

  if (class_exists('xp', false)) {
    foreach ($bootstrap['overlay'] as $path) { \lang\ClassLoader::registerPath($path, true); }
    foreach ($bootstrap['local'] as $path) { \lang\ClassLoader::registerPath($path); }
  } else if (isset($bootstrap['base'])) {
    $paths= array_merge($bootstrap['overlay'], $bootstrap['core'], $bootstrap['local']);
    require $bootstrap['base'];
  } else {
    $paths= array_merge($bootstrap['overlay'], $bootstrap['core'], $bootstrap['local']);
    require dirname(__FILE__).DIRECTORY_SEPARATOR.'lang.base.php';
  }

  ini_set('error_prepend_string', EPREPEND_IDENTIFIER);
  set_exception_handler('__except');
  ob_start('__output');

  $ext= substr($argv[1], -4, 4);
  if ('.php' === $ext) {
    if (false === ($uri= realpath($argv[1]))) {
      xp::error('Cannot load '.$argv[1].' - does not exist');
    }
    if (is(null, ($cl= \lang\ClassLoader::getDefault()->findUri($uri)))) {
      xp::error('Cannot load '.$argv[1].' - not in class path');
    }
    $class= $cl->loadUri($uri);
  } else if ('.xar' === $ext) {
    if (false === ($uri= realpath($argv[1]))) {
      xp::error('Cannot load '.$argv[1].' - does not exist');
    }
    $cl= \lang\ClassLoader::registerPath($uri);
    if (!$cl->providesResource('META-INF/manifest.ini')) {
      xp::error($cl->toString().' does not provide a manifest');
    }
    $class= $cl->loadClass(this(parse_ini_string($cl->getResource('META-INF/manifest.ini')), 'main-class'));
  } else {
    $class= \lang\XPClass::forName($argv[1]);
  }

  array_shift($_SERVER['argv']);
  try {
    exit($class->getMethod('main')->invoke(NULL, array(array_slice($argv, 2))));
  } catch (\lang\SystemExit $e) {
    if ($message= $e->getMessage()) echo $message, "\n";
    exit($e->getCode());
  }
?>
