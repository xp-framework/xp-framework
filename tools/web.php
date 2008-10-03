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
      trigger_error('[bootstrap] Cannot determine boot class path from '.realpath($path), E_USER_ERROR);
      exit(0x3d);
    }
    return $inc;
  }
  // }}}

  $webroot= getenv('DOCUMENT_ROOT').'/..';
  set_include_path(
    scanpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..', $webroot, TRUE).
    scanpath($webroot, $webroot).
    get_include_path()
  );

  // Bootstrap 
  require('lang.base.php');
  uses('sapi.scriptlet.ScriptletRunner', 'util.PropertyManager', 'rdbms.ConnectionManager');
  
  $pm= PropertyManager::getInstance();
  $pm->configure($webroot.'/etc');
  $pr= $pm->getProperties('web');

  $url= getenv('SCRIPT_URL');
  $specific= 'app@'.getenv('SERVER_NAME');
  $mappings= $pr->readHash($specific, 'mappings', $pr->readHash('app', 'mappings'));
  foreach ($mappings->keys() as $pattern) {
    if (!preg_match('°'.$pattern.'°', $url)) continue;
    
    // Run first scriptlet that matches
    $scriptlet= $mappings->get($pattern);
    $class= XPClass::forName($pr->readString($scriptlet, 'class'));
    $args= array();
    foreach ($pr->readArray($scriptlet, 'init-params') as $value) {
      $args[]= strtr($value, array('{WEBROOT}' => $webroot));
    }
    
    // HACK #1: Always make connection manager available - should be done inside scriptlet init
    ConnectionManager::getInstance()->configure($pm->getProperties('database'));
    
    // HACK #2: Parse URL - should be done inside XMLScriptlet
    preg_match('°^/([^/]+)°', $url, $patterns);
    putenv('STATE='.(isset($patterns[1]) ? $patterns[1] : 'home'));
    putenv('PRODUCT=planet');
    putenv('LANGUAGE=en_US');
    
    $r= new ScriptletRunner($pr->readInteger($specific, 'flags', $pr->readInteger('app', 'flags', 0)));
    exit($r->run($class->getConstructor()->newInstance($args)));
  }
  
  trigger_error('[scriptlet] No scriptlet at url "'.htmlspecialchars($url).'"', E_USER_ERROR);
?>
