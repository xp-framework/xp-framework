<?php
  error_reporting(E_ALL);

  function import($str) {
    include_once(
      SKELETON_PATH.
      strtr($str, array('.' => '/', '~' => '..', '_' => '.')).
      '.class.php'
    );
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
  
  import('lang.Object');
  import('lang.Exception');
  register_shutdown_function('destroy');
?>
