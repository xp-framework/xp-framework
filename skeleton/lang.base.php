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
        strtr($str, array('.' => '/', '~' => '..', '_' => '.', '%' => '_')).
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
    set_error_handler('error');
    $GLOBALS['php_errormessage']=  array();
    $GLOBALS['php_errorline']= array();
    $GLOBALS['php_errorfile']= array();
    $GLOBALS['php_errorcode']= array();
    $GLOBALS['php_exceptions']= array();
  }
  
  function is_error() {
    return empty($GLOBALS['php_errormessage']) ? FALSE : $GLOBALS['php_errormessage'];
  }
 
  if (!function_exists('is_a')) { function is_a(&$object, $name) {
     return (
      (get_class($object) == strtolower($name)) ||
      (is_subclass_of($object, $name))
    );
  }}
  
  if (!function_exists('var_export')) { function var_export(&$data, $return) {
    ob_start();
    var_dump($data);
    $dump= ob_get_contents();
    ob_end_clean();
    if ($return) return $dump;
    
    echo $dump;
  }}
  
  function &cast(&$var, $type= NULL) {
    if (NULL == $var) return NULL;
    if (NULL != $type) settype($var, $type);
    return $var;
  }

  function catch($name, &$e) {
    restore_error_handler();
    $return= FALSE;
    foreach (array_keys($GLOBALS['php_exceptions']) as $i) {
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
    'lang.XPClass',
    'lang.Exception',
    'lang.IllegalAccessException',
    'lang.IllegalArgumentException',
    'lang.IllegalStateException',
    'lang.FormatException',
    'lang.ClassLoader'
  );
  register_shutdown_function('destroy');
  //}}}
?>
