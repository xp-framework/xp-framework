<?php
/* This file provides the core for the XP framework
 * 
 * $Id$
 */

  // {{{ final class xp
  class xp {
  
    // {{{ public string nameOf(string name)
    //     Returns the fully qualified name
    function nameOf($name) {
      if (!($n= xp::registry('class.'.$name))) {
        return 'php.'.$name;
      }
      return $n;
    }
    // }}}

    // {{{ public bool errorAt(string file [, int line)
    //     Returns whether an error occured at the specified position
    function errorAt($file, $line= -1) {
      $errors= &xp::registry('errors');
      
      // If no line is requested, this is O(n)
      if ($line < 0) return !empty($errors[$file]);
      
      // Else, we'll have to search...
      if (isset($errors[$file])) for (
        $i= 0, $s= sizeof($errors[$file]); 
        $i < $s; 
        $i++
      ) {
        if ($line == $errors[$file][$i]['line']) return TRUE;
      }
      
      return FALSE;
    }
    // }}}
    
    // {{{ internal mixed registry(mixed args*)
    //     Stores static data
    function &registry() {
      static $registry= array();
      
      switch (func_num_args()) {
        case 0: return $registry;
        case 1: return $registry[func_get_arg(0)];
        case 2: $registry[func_get_arg(0)]= func_get_arg(1); break;
      }
    }
    // }}}
    
    // {{{ internal void error(int code, string msg, string file, int line)
    //     Error callback
    function error($code, $msg, $file, $line) {
      if (0 == error_reporting()) return;
      
      $errors= &xp::registry('errors');
      $errors[$file][]= array($code, $msg, $line);
      xp::registry('errors', $errors);
    }
    // }}}
    
    // {{{ internal string reflect(string str)
    //     Retrieve PHP conformant name for fqcn
    function reflect($str) {
      return strtolower(substr($str, strrpos($str, '.')+ 1));
    }
    // }}}
    
    // {{{ internal void destroy(void)
    //     Shutdown function
    function destroy() {
      error_reporting(0); // Shut up!
      foreach (array_keys($GLOBALS) as $var) {
        if (
          is_object($GLOBALS[$var]) && 
          method_exists($GLOBALS[$var], '__destruct')
        ) $GLOBALS[$var]->__destruct();
      }
    }
    // }}}
  }
  // }}}

  // {{{ bool uses (string* args)
  //     Uses one or more classes
  function uses() {
    $result= TRUE;
    foreach (func_get_args() as $str) {
      $result= $result & include_once(
        strtr($str, '.', DIRECTORY_SEPARATOR).
        '.class.php'
      );
      xp::registry('class.'.xp::reflect($str), $str);
    }
    return $result;
  }
  // }}}

  // {{{ void try (void)
  //     Begins a try ... catch block
  function try() {
    set_error_handler(array('xp', 'error'));
  }
  // }}}

  // {{{ bool catch (string name, &lang.Exception e)
  //     Ends a try ... catch block
  function catch($name, &$e) {
    restore_error_handler();
    $exceptions= &xp::registry('exceptions');
    
    $return= FALSE;
    foreach (array_keys($exceptions) as $i) {
      if (is_a($exceptions[$i], $name)) {
        $e= $exceptions[$i];       // Intentional copy
        unset($exceptions[$i]);
        $return= TRUE;
      }
    }
    return $return;
  }
  // }}}

  // {{{ void finally (void)
  //     Syntactic sugar. Intentionally empty
  function finally() {
  }
  // }}}

  // {{{ bool throw (lang.Exception e)
  //     throws an exception
  function throw(&$e) {
    $exceptions= &xp::registry('exceptions');
    $exceptions[]= &$e;
    xp::registry('exceptions', $exceptions);
    return FALSE;
  }
  // }}}

  // {{{ mixed cast (&mixed var, mixed type default NULL)
  //     Casts. If var === NULL, it won't be touched
  function &cast(&$var, $type= NULL) {
    if (NULL === $var) return NULL;

    switch ($type) {
      case NULL: 
        break;

      case 'int':
      case 'integer':
      case 'float':
      case 'double':
      case 'string':
      case 'bool':
      case 'null':
        if (is_a($var, 'Object')) $var= $var->toString();
        settype($var, $type);
        break;

      case 'array':
      case 'object':
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
  // }}}

  // {{{ initialization
  error_reporting(E_ALL);
  define('SKELETON_PATH', (getenv('SKELETON_PATH')
    ? getenv('SKELETON_PATH')
    : dirname(__FILE__).'/'
  ));
  ini_set('include_path', SKELETON_PATH.':'.ini_get('include_path'));
  register_shutdown_function(array('xp', 'destroy'));
  
  xp::registry('errors', array());
  xp::registry('exceptions', array());

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
  
  // }}}
?>
