<?php
/* This file provides the core for the XP framework
 * 
 * $Id: lang.base.php 5786 2005-09-13 08:29:22Z kiesel $
 */

  // {{{ final class xp
  class xp {
  
    // {{{ public string nameOf(string name)
    //     Returns the fully qualified name
    function nameOf($name) {
      if (!($n= xp::registry('class.'.$name))) {
        return $name ? 'php.'.$name : NULL;
      }
      return $n;
    }
    // }}}

    // {{{ public string typeOf(&mixed arg)
    //     Returns the fully qualified type name
    function typeOf(&$arg) {
      return is_object($arg) ? xp::nameOf(get_class($arg)) : gettype($arg);
    }
    // }}}

    // {{{ public string stringOf(&mixed arg)
    //     Returns a string representation of the given argument
    function stringOf(&$arg) {
      return is_a($arg, 'Object') ? $arg->toString() : var_export($arg, 1);
    }
    // }}}

    // {{{ public void gc()
    //     Runs the garbage collector
    function gc() {
      xp::registry('errors', array());
      xp::registry('exceptions', array());
    }
    // }}}

    // {{{ public <null> null()
    //     Runs a fatal-error safe version of NULL
    function &null() {
      return xp::registry('null');
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
        if ($line == $errors[$file][$i][2]) return TRUE;
      }
      
      return FALSE;
    }
    // }}}
    
    // {{{ public mixed sapi(string* sapis)
    //     Sets an SAPI
    function sapi() {
      foreach ($a= func_get_args() as $name) {
        require_once('sapi'.DIRECTORY_SEPARATOR.strtr($name, '.', DIRECTORY_SEPARATOR).'.sapi.php');
      }
      xp::registry('sapi', $a);
    }
    // }}}
    
    // {{{ internal mixed registry(mixed args*)
    //     Stores static data
    function &registry() {
      static $registry= array();
      static $nullref= NULL;
      
      switch (func_num_args()) {
        case 0: return $registry;
        case 1: return $registry[func_get_arg(0)];
        case 2: $registry[func_get_arg(0)]= func_get_arg(1); break;
      }
      return $nullref;
    }
    // }}}
    
    // {{{ internal string reflect(string str)
    //     Retrieve PHP conformant name for fqcn
    function reflect($str) {
      return strtolower(substr($str, (FALSE === $p= strrpos($str, '.')) ? 0 : $p+ 1));
    }
    // }}}

    // {{{ internal void error(string message)
    //     Throws a fatal error and exits with exitcode 61
    function error($message) {
      restore_error_handler();
      trigger_error($message, E_USER_ERROR);
      exit(0x3d);
    }
  }
  // }}}

  // {{{ final class null
  class null {

    // {{{ public object null(void)
    //     Constructor to avoid magic __call invokation
    function null() { }
    // }}}
    
    // {{{ magic bool __call(string name, mixed[] args, &mixed return)
    //     Call proxy
    function __call($name, $args, &$return) {
      $return= &throw(new NullPointerException('Method.invokation('.$name.')'));
      return FALSE;
    }
    // }}}

    // {{{ magic bool __set(string name, mixed value)
    //     Set proxy
    function __set($name, $value) {
      throw(new NullPointerException('Property.write('.$name.')'));
      return FALSE;
    }
    // }}}

    // {{{ magic bool __get(string name, &mixed value)
    //     Set proxy
    function __get($name, &$value) {
      $value= &throw(new NullPointerException('Property.read('.$name.')'));
      return FALSE;
    }
    // }}}
  }
  // }}}

  // {{{ internal void __error(int code, string msg, string file, int line)
  //     Error callback
  function __error($code, $msg, $file, $line) {
    if (0 == error_reporting() || is_null($file)) return;

    $errors= &xp::registry('errors');
    $errors[$file][]= array($code, $msg, $line);
    xp::registry('errors', $errors);
  }
  // }}}

  // {{{ internal void __destroy(void)
  //     Shutdown function
  function __destroy() {
    foreach (array_keys($GLOBALS) as $k) {
      if (is_a($GLOBALS[$k], 'Object')) {
        $GLOBALS[$k]->__destruct();
      }
    }
  }
  // }}}

  // {{{ void uses (string* args)
  //     Uses one or more classes
  function uses() {
    foreach (func_get_args() as $str) {
      if (class_exists($class= xp::reflect($str))) continue;

      if ($p= strpos($str, '+xp://')) {
        $type= substr($str, 0, $p);
        
        // Load stream wrapper implementation and register it if not done so before
        if (!class_exists('uwrp·'.$type)) {
          require('sapi'.DIRECTORY_SEPARATOR.$type.'.uwrp.php');
          stream_wrapper_register($type.'+xp', 'uwrp·'.$type);
        }

        // Load using wrapper
        if (FALSE === include($str)) {
          xp::error(xp::stringOf(new Error('Cannot include '.$str)));
        }
        $str= substr($str, strrpos($str, '/')+ 1);
        $class= xp::reflect($str);
      } else {
        if (FALSE === include(strtr($str, '.', DIRECTORY_SEPARATOR).'.class.php')) {
          xp::error(xp::stringOf(new Error('Cannot include '.$str)));
        }
      }
      
      // Register class name and call static initializer if available
      xp::registry('class.'.$class, $str);
      is_callable(array($class, '__static')) && call_user_func(array($class, '__static'));
    }
  }
  // }}}

  // {{{ void try (void)
  //     Begins a try ... catch block
  function try() {
  }
  // }}}

  // {{{ bool catch (string name, &lang.Exception e)
  //     Ends a try ... catch block
  function catch($name, &$e) {
    $exceptions= &xp::registry('exceptions');
    
    $return= FALSE;
    foreach (array_keys($exceptions) as $i) {
      if (is($name, $exceptions[$i])) {
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

  // {{{ null throw (lang.Exception e)
  //     throws an exception
  function &throw(&$e) {
    $exceptions= &xp::registry('exceptions');
    $exceptions[]= &$e;
    xp::registry('exceptions', $exceptions);
    return xp::registry('null');
  }
  // }}}

  // {{{ null raise (string classname, string message)
  //     throws an exception by a given class name
  function &raise($classname, $message) {
    try(); {
      $class= &XPClass::forName($classname);
    } if (catch('ClassNotFoundException', $e)) {
      xp::error($e->getMessage());
    }
    $exceptions= &xp::registry('exceptions');
    $exceptions[]= &$class->newInstance($message);
    xp::registry('exceptions', $exceptions);
    return xp::registry('null');
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

  // {{{ proto void implements(string file, string interface [, string interface [, ...]]) 
  //     Defines that the class this is called in implements certain interface(s)
  function implements() {
    $class= strtolower(substr(basename(func_get_arg(0)), 0, -10));
    $signature= array_flip(get_class_methods($class));
    $implements= xp::registry('implements');
    
    for ($i= 1, $s= func_num_args(); $i < $s; $i++) {
      $interface= func_get_arg($i);
      uses($interface);
      $name= xp::reflect($interface);
      $methods= array_flip(get_class_methods($name));
      
      // Get rid of constructors
      $c= $name;
      do {
        unset($methods[$c]);
        $implements[$class][$c]= 1;
      } while ($c= get_parent_class($c));

      // Pop off 'lang.Interface'
      array_pop($implements[$class]);

      // Check implementation
      foreach (array_keys($methods) as $method) {
        if (!isset($signature[$method])) {
          xp::error('Interface method '.$interface.'::'.$method.'() not implemented by class '.$class);
        }
      }
    }
    
    xp::registry('implements', $implements);
  }
  // }}}
  
  // {{{ proto bool is(string class, &lang.Object object)
  //     Checks whether a given object is of the class, a subclass or implements an interface
  function is($class, &$object) {
    $p= get_class($object);
    if (is_null($class) && 'null' == $p) return TRUE;
    $class= xp::reflect($class);
    if (is_a($object, $class)) return TRUE;
    $implements= xp::registry('implements');
    
    do {
      if (isset($implements[$p][$class])) return TRUE;
    } while ($p= get_parent_class($p));
    return FALSE;
  }
  // }}}

  // {{{ proto void delete(&lang.Object object)
  //     Destroys an object
  function delete(&$object) {
    is_a($object, 'Object') && $object->__destruct();
    $object= NULL;
  }
  // }}}

  // {{{ proto lang.Object &clone(lang.Object object) throws CloneNotSupportedException
  //     Clones an object
  function &clone($object) {
    $object->__id= microtime();
    if (is_callable(array(&$object, '__clone'))) {
      try(); {
        call_user_func(array(&$object, '__clone'));
      } if (catch('CloneNotSupportedException', $e)) {
        return throw($e);
      }
    }
    return $object;
  }

  // {{{ proto void with(expr)
  //     Syntactic sugar. Intentionally empty
  function with() {
  }
  // }}}

  // {{{ initialization
  error_reporting(E_ALL);
  if (!defined('PATH_SEPARATOR')) {
    define('PATH_SEPARATOR',  0 == strncasecmp('WIN', PHP_OS, 3) ? ';' : ':');    
  }
  define('SKELETON_PATH', (getenv('SKELETON_PATH')
    ? getenv('SKELETON_PATH')
    : dirname(__FILE__).DIRECTORY_SEPARATOR
  ));
  define('LONG_MAX', is_int(2147483648) ? 9223372036854775807 : 2147483647);
  define('LONG_MIN', -LONG_MAX - 1);
  ini_set('include_path', SKELETON_PATH.PATH_SEPARATOR.ini_get('include_path'));
  register_shutdown_function('__destroy');
  if (extension_loaded('overload')) overload('null');
  xp::registry('null', new null());
  xp::registry('errors', array());
  xp::registry('exceptions', array());
  xp::registry('class.xp', '<xp>');
  xp::registry('class.null', '<null>');
  set_error_handler('__error');

  require(SKELETON_PATH.'__core__.php');
  // }}}
?>
