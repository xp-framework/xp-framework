<?php
  error_reporting(E_ALL);

  // @deprecated
  function import($str) {
    return include_once(
      SKELETON_PATH.
      strtr($str, array('.' => '/', '~' => '..', '_' => '.')).
      '.class.php'
    );
  }
  
  function uses() {
    $result= TRUE;
    foreach (func_get_args() as $str) $result= $result & include_once(
      SKELETON_PATH.
      strtr($str, array('.' => '/', '~' => '..', '_' => '.')).
      '.class.php'
    );
    return $result;
  }
  
  function destroy() {
    error_reporting(0); // Shut up!
    foreach (array_keys($GLOBALS) as $var) {
      if (!is_object($GLOBALS[$var]) || !method_exists($GLOBALS[$var], '__destruct')) continue;
      $GLOBALS[$var]->__destruct();
    }
  }
  
  // Class Files
  define('SKELETON_PATH', ('' != getenv('SKELETON_PATH')
    ? getenv('SKELETON_PATH')
    : dirname(__FILE__).'/'
  ));
  
  uses('lang.Object', 'lang.Exception');
  register_shutdown_function('destroy');
?>
