<?php
/* Diese Datei ist Teil des XP-Frameworks
 * 
 * $Id$
 */

  function reflect($str) {
    return strtolower(substr($str, strrpos($str, '.')+ 1));
  }

  function uses() {
    $result= TRUE;
    foreach (func_get_args() as $str) {
      $result= $result & include_once(
        SKELETON_PATH.
        strtr($str, array('.' => '/', '~' => '..', '_' => '.')).
        '.class.php'
      );
      $GLOBALS['php_class_names'][reflect($str)]= $str;
    }
    return $result;
  }
  
  function destroy() {
    error_reporting(0); // Shut up!
    foreach (array_keys($GLOBALS) as $var) {
      if (!is_object($GLOBALS[$var]) || !method_exists($GLOBALS[$var], '__destruct')) continue;
      $GLOBALS[$var]->__destruct();
    }
  }
   
  function try() {
    $GLOBALS['php_errormessage']=  array();
    $GLOBALS['php_errorline']= array();
    $GLOBALS['php_errorfile']= array();
    $GLOBALS['php_errorcode']= array();
    $GLOBALS['php_exceptions']= array();
  }
 
  function is_a(&$object, $name) {
     return (
      (get_class($object) == strtolower($name)) ||
      (is_subclass_of($object, $name))
    );
  }

  function catch($name, &$e) {
    restore_error_handler();
    $return= FALSE;
    for ($i= 0; $i< sizeof($GLOBALS['php_exceptions']); $i++) {
      if (is_a($GLOBALS['php_exceptions'][$i], $name)) {
        $e= $GLOBALS['php_exceptions'][$i];
        unset($GLOBALS['php_exceptions'][$i]);
        $return= TRUE;
      }
    }
    return $return;
  }
  
  function throw(&$e) {
    $GLOBALS['php_exceptions'][]= &$e;
    return FALSE;
  }
  
  function error($errno, $errstr, $errfile, $errline) {
    if (0 == error_reporting()) return;
    $GLOBALS['php_errormessage'][]= $errstr;
    $GLOBALS['php_errorline'][]= $errline;
    $GLOBALS['php_errorfile'][]= $errfile;
    $GLOBALS['php_errorcode'][]= $errno;
  }
  
  // Class Files
  define('SKELETON_PATH', ('' != getenv('SKELETON_PATH')
    ? getenv('SKELETON_PATH')
    : dirname(__FILE__).'/'
  ));

  //{{{ main
  error_reporting(E_ALL);
  uses(
    'lang.Object', 
    'lang.Exception',
    'lang.IllegalStateException',
    'lang.IllegalArgumentException',
    'lang.FormatException',
    'lang.ClassLoader'
  );
  set_error_handler('error');
  register_shutdown_function('destroy');
  //}}}
?>
