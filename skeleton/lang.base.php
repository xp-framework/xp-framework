<?php
/* This file provides the core for the XP framework
 * 
 * $Id$
 */

  function reflect($str) {
    return strtolower(substr($str, strrpos($str, '.')+ 1));
  }

  define('SKELETON_PATH', ('' != getenv('SKELETON_PATH')
    ? getenv('SKELETON_PATH')
    : dirname(__FILE__).'/'
  ));
  ini_set('include_path', SKELETON_PATH.':'.ini_get('include_path'));

  function clear_error() {
    unset($GLOBALS['php_errormessage']);
  }

  function is_error() {
    return empty($GLOBALS['php_errormessage']) ? FALSE : $GLOBALS['php_errormessage'];
  }
  
  if (!extension_loaded('xp')) {

    function uses() {
      $result= TRUE;
      foreach (func_get_args() as $str) {
        $result= $result & include_once(
          strtr($str, '.', DIRECTORY_SEPARATOR).
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

    function finally() {
      // Intentionally empty
    }

    function &cast(&$var, $type= NULL) {
      if (NULL == $var) return NULL;

      switch ($type) {
        case NULL: 
          break;

        case 'int':
        case 'integer':
        case 'float':
        case 'double':
        case 'string':
        case 'array':
        case 'object':
        case 'bool':
        case 'null':
          settype($var, $type);
          break;

        default:
          // Cast to an object of "$type"
          $o= &new $type;
          if (is_object($var) || is_array($var)) {
            foreach ($var as $k => $v) {
              $o->$k= $v;
            }
          } else {
            $o->scalar= $var;
          }
          return $o;
          break;
      }
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

    error_reporting(E_ALL);
    uses(
      'lang.Object'
    );

    register_shutdown_function('destroy');
  }
  
  uses(
    'lang.XPClass',
    'lang.Exception',
    'lang.IllegalAccessException',
    'lang.IllegalArgumentException',
    'lang.IllegalStateException',
    'lang.FormatException',
    'lang.ClassLoader'
  );

?>
