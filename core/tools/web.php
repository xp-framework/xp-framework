<?php
  if (version_compare(PHP_VERSION, '5.3.0', '<')) {
    trigger_error('This version of the XP Framework requires PHP 5.3.0+, have PHP '.PHP_VERSION, E_USER_ERROR);
    exit(0x3d);
  }
  $webroot= getenv('WEB_ROOT') ?: $_SERVER['DOCUMENT_ROOT'].'/..';
  $configd= ini_get('user_dir') ?: $webroot.'/etc';

  // Set error status to 500 by default - if a fatal error occurs,
  // this guarantees to at least send an error code.
  switch (php_sapi_name()) {
    case 'cgi':
      header('Status: 500 Internal Server Error');
      break;

    case 'fpm-fcgi': {
      header('HTTP/1.0 500 Internal Server Error');
      $_SERVER['SCRIPT_URL']= substr($_SERVER['REQUEST_URI'], 0, strcspn($_SERVER['REQUEST_URI'], '?#'));
      $_SERVER['SERVER_PROFILE']= getenv('SERVER_PROFILE');
      break;
    }

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

  $bootstrap= bootstrap($webroot, $webroot);
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
  
  exit(\xp\scriptlet\Runner::main(array($webroot, $configd, $_SERVER['SERVER_PROFILE'], $_SERVER['SCRIPT_URL'])));
?>
