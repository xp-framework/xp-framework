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

    // {{{ public string typeOf(&mixed arg)
    //     Returns the fully qualified type name
    function typeOf(&$arg) {
      return is_object($arg) ? xp::nameOf(get_class($arg)) : gettype($arg);
    }
    // }}}

    // {{{ public void gc()
    //     Runs the garbage collector
    function gc() {
      xp::registry('errors', array());
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
      
      switch (func_num_args()) {
        case 0: return $registry;
        case 1: return $registry[func_get_arg(0)];
        case 2: $registry[func_get_arg(0)]= func_get_arg(1); break;
      }
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
  class null {

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

    $errors= &xp::registry('errors');
    $errors[$file][]= array($code, $msg, $line);
    xp::registry('errors', $errors);
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
      $result= $result & include_once(
        strtr($str, '.', DIRECTORY_SEPARATOR).
        '.class.php'
      );
      xp::registry('class.'.xp::reflect($str), $str);
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

  // {{{ proto bool is(string class, &lang.Object object)
  //     Checks whether a given object is of the class, a subclass or implements an interface
  function is($class, &$object) {
    return is_a($object, xp::reflect($class));
  }
  // }}}

  // {{{ proto void delete(&lang.Object object)
  //     Destroys an object
  function delete(&$object) {
    $object->__destruct();
    unset($object);
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
  xp::registry('null', new null());
  xp::registry('errors', array());
  set_error_handler('__error');

  uses(
    'lang.Object',
    'lang.XPClass',
    'lang.XPException',
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
