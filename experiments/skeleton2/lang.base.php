<?php
/* This file provides the core for the XP framework
 * 
 * $Id$
 */

  // {{{ final class xp
  final class xp {
    public static 
      $errors   = array(),
      $classes  = array(),
      $sapi     = array(),
      $null     = NULL;
  
    // {{{ public string nameOf(string name)
    //     Returns the fully qualified name
    function nameOf($name) {
      $name= strtolower($name);
      if (!isset(xp::$classes[$name])) {
        return 'php.'.$name;
      }
      return xp::$classes[$name];
    }
    // }}}

    // {{{ public string typeOf(&mixed arg)
    //     Returns the fully qualified type name
    function typeOf(&$arg) {
      return is_object($arg) ? xp::nameOf(get_class($arg)) : gettype($arg);
    }
    // }}}

    // {{{ public void gc()
    //     Runs the garbage collector
    function gc() {
      xp::$errors= array();
    }
    // }}}

    // {{{ public bool errorAt(string file [, int line)
    //     Returns whether an error occured at the specified position
    function errorAt($file, $line= -1) {

      // If no line is requested, this is O(n)
      if ($line < 0) return !empty(xp::$errors[$file]);
      
      // Else, we'll have to search...
      if (isset(xp::$errors[$file])) for (
        $i= 0, $s= sizeof(xp::$errors[$file]); 
        $i < $s; 
        $i++
      ) {
        if ($line == xp::$errors[$file][$i]['line']) return TRUE;
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
      xp::$sapi= $a;
    }
    // }}}
    
    // {{{ internal string reflect(string str)
    //     Retrieve PHP conformant name for fqcn
    function reflect($str) {
      return strtolower(substr($str, (FALSE === $p= strrpos($str, '.')) ? 0 : $p+ 1));
    }
    // }}}

    // {{{ internal void error(string message)
    //     Throws a fatal error and exits with exitcode 127
    function error($message) {
      restore_error_handler();
      trigger_error($message, E_USER_ERROR);
      exit(0x7f);
    }
  }
  // }}}

  // {{{ final class null
  final class null {

    // {{{ public object null(void)
    //     Constructor to avoid magic __call invokation
    function null() { }
    // }}}
    
    // {{{ magic mixed __call(string name, mixed[] args)
    //     Call proxy
    function __call($name, $args) {
      throw(new NullPointerException('Method.invokation('.$name.')'));
    }
    // }}}

    // {{{ magic void __set(string name, mixed value)
    //     Set proxy
    function __set($name, $value) {
      throw(new NullPointerException('Property.write('.$name.')'));
    }
    // }}}

    // {{{ magic mixed __get(string name)
    //     Set proxy
    function __get($name) {
      throw(new NullPointerException('Property.read('.$name.')'));
    }
    // }}}
  }
  // }}}
  
  // {{{ internal void __error(int code, string msg, string file, int line)
  //     Error callback
  function __error($code, $msg, $file, $line) {
    if (0 == error_reporting()) return;

    xp::$errors[$file][]= array($code, $msg, $line);
  }
  // }}}

  // {{{ public bool null(mixed arg)
  //     Checks whether a given argument is NULL or object(null)
  function null($arg) {
    return (is_object($arg) && 'null' == get_class($arg)) || is_null($arg);
  }
  // }}}

  // {{{ internal void __destroy(void)
  //     Shutdown function
  function __destroy() {
  }
  // }}}

  // {{{ bool uses (string* args)
  //     Uses one or more classes
  function uses() {
    $result= TRUE;
    foreach (func_get_args() as $str) {
      if ($i= include_once(strtr($str, '.', DIRECTORY_SEPARATOR).'.class.php')) {
        $class= xp::reflect($str);
        xp::$classes[$class]= $str;
        is_callable(array($class, '__static')) && call_user_func(array($class, '__static'));
      }
      $result= $result & $i;
    }
    return $result;
  }
  // }}}

  // {{{ void finally (void)
  //     Syntactic sugar. Intentionally empty
  function finally() {
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

  // {{{ proto bool is(string class, lang.Object object)
  //     Checks whether a given object is of the class, a subclass or implements an interface
  function is($class, $object) {
    return is_a($object, xp::reflect($class));
  }
  // }}}

  // {{{ proto void delete(lang.Object object)
  //     Destroys an object
  function delete($object) {
    $object= NULL;
  }
  // }}}

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
  ini_set('include_path', SKELETON_PATH.PATH_SEPARATOR.ini_get('include_path'));
  register_shutdown_function('__destroy');
  xp::$null= new null();
  set_error_handler('__error');

  uses(
    'lang.Generic',
    'lang.Object',
    'lang.XPException',
    'lang.XPClass',
    'lang.Error',
    'lang.NullPointerException',
    'lang.IllegalAccessException',
    'lang.IllegalArgumentException',
    'lang.IllegalStateException',
    'lang.FormatException',
    'lang.ClassLoader'
  );
  
  // }}}
?>
