<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$ 
 */
  error_reporting(E_ALL);
  
  define('SKELETON_PATH', 
    dirname(__FILE__).DIRECTORY_SEPARATOR.'skeleton'.DIRECTORY_SEPARATOR
    .PATH_SEPARATOR.  
    dirname(__FILE__).DIRECTORY_SEPARATOR.'ports'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR
  );
  ini_set('include_path', '.'.PATH_SEPARATOR.SKELETON_PATH);

  class XPException extends Exception {
    public $cause= NULL;
    public static $instance= NULL;

    function __toString() { 
      return $this->cause->toString(); 
    }
  }
  
  function load_class_file($qualified) {
    if (file_exists($qualified.'.php5') && filemtime($qualified.'.php5') >= filemtime($qualified.'.xp')) {
      return include($qualified.'.php5');
    }

    // Could not find the file, compile
    $cmd= sprintf(getenv('COMPILE_CMD'), $qualified.'.xp');
    echo '*** Compiling ', $qualified, ' using (', $cmd, ")\n";
    passthru($cmd);
    return include($qualified.'.php5');
  }
  
  function include_class($class) {
    $file= strtr($class, '·', DIRECTORY_SEPARATOR);
    foreach (explode(PATH_SEPARATOR, ini_get('include_path')) as $path) {
      $qualified= $path.DIRECTORY_SEPARATOR.$file;
      if (!file_exists($qualified.'.xp')) continue;
      return load_class_file($qualified);
    }
    
    return FALSE;
  }
  
  function uses() {
    foreach (func_get_args() as $class) {
      $fqcn= strtr($class, './\\', '···');
      if (class_exists($fqcn) || interface_exists($fqcn)) continue;
      if (FALSE === include_class($fqcn)) {
        throw new Exception('*** Cannot include '.$fqcn."\n");
      }
      is_callable(array($fqcn, '__static')) && call_user_func(array($fqcn, '__static'));
    }
  }
  
  function delete(&$o) {
    $o= NULL;
  }
  
  final class null {
    public function __construct() { 
      if (xp::$null) throw xp::exception(new lang·IllegalAccessException('New'));
    }

    public function __set($prop, $value) { 
      throw xp::exception(new lang·NullPointerException('Set: '.$prop));
    }

    public function __get($prop) { 
      throw xp::exception(new lang·NullPointerException('Get: '.$prop));
    }

    public function __call($method, $args) { 
      throw xp::exception(new lang·NullPointerException('Invoke: '.$method));
    }

    public function __clone() { 
      throw xp::exception(new lang·NullPointerException('Clone: '.$method));
    }
    
    public function __toString() {
      return '<null>';
    }
  }
  
  function is($name, $object) { 
    if (NULL === $name && $object instanceof null) return TRUE;

    $class= xp::reflect($name);
    return $object instanceof $class; 
  }

  function raise($name, $message) { 
    uses($name);
    $class= xp::reflect($name);
    throw xp::exception(new $class($message));
  }
  
  final class arraywrapper {
    public $backing= array();
    
    public function __construct($array) {
      $this->backing= $array;
    }
  }

  final class xp {
    public static $null;
    public static $registry= array();
    
    public static function wraparray($array) {
      return new arraywrapper($array);
    }
    
    public static function null() {
      return xp::$null;
    }
    
    public static function reflect($name) {
      return strtr($name, '.', '·');
    }

    public static function nameOf($name) {
      return strtr($name, '·', '.');
    }

    public static function cast($var, $type) {
      settype($var, $type);
      return $var;
    }

    public static function stringOf($arg, $indent= '') {
      static $protect= array();

      if ($arg instanceof lang·Object && !isset($arg->__id)) {
        $arg->__id= microtime();
      }
      
      if (is_string($arg)) {
        return '"'.$arg.'"';
      } else if (is_bool($arg)) {
        return $arg ? 'true' : 'false';
      } else if (is_null($arg)) {
        return 'null';
      } else if (is_a($arg, 'null')) {
        return '<null>';
      } else if (is_int($arg) || is_float($arg)) {
        return (string)$arg;
      } else if ($arg instanceof lang·Object && !isset($protect[$arg->__id])) {
        $protect[$arg->__id]= TRUE;
        $s= $arg->toString();
        unset($protect[$arg->__id]);
        return $s;
      } else if (is_array($arg)) {
        $ser= serialize($arg);
        if (isset($protect[$ser])) return '->{:recursion:}';
        $protect[$ser]= TRUE;
        $r= "[\n";
        foreach (array_keys($arg) as $key) {
          $r.= $indent.'  '.$key.' => '.xp::stringOf($arg[$key], $indent.'  ')."\n";
        }
        unset($protect[$ser]);
        return $r.$indent.']';
      } else if (is_object($arg)) {
        $ser= serialize($arg);
        if (isset($protect[$ser])) return '->{:recursion:}';
        $protect[$ser]= TRUE;
        $r= xp::nameOf(get_class($arg))." {\n";
        $vars= (array)$arg;
        foreach (array_keys($vars) as $key) {
          $r.= $indent.'  '.$key.' => '.xp::stringOf($vars[$key], $indent.'  ')."\n";
        }
        unset($protect[$ser]);
        return $r.$indent.'}';
      } else if (is_resource($arg)) {
        return 'resource(type= '.get_resource_type($arg).', id= '.(int)$arg.')';
      }
    }

    public static function typeOf($expr) {
      if ($expr instanceof lang·Object) {
        return $expr->getClassName();
      } else if ($expr instanceof null) {
        return '<null>';
      }
      return gettype($expr);
    }
    
    public static function gc() {
      xp::$registry['errors']= array();
    }

    public static function errorAt($file, $line= -1) {
      $errors= xp::$registry['errors'];
      
      // If no line is given, check for an error in the file
      if ($line < 0) return !empty($errors[$file]);
      
      // Otherwise, check for an error in the file on a certain line
      return !empty($errors[$file][$line]);
    }

    public static function registry($key= NULL, $value= NULL) {
      switch (func_num_args()) {
        case 0: return xp::$registry;
        case 1: return xp::$registry[$key];
        case 2: xp::$registry[$key]= $value;
      }
    }

    public static function exception(lang·Throwable $e) {
      XPException::$instance->cause= $e;
      return XPException::$instance;
    }

    public static function spawn($class, $ctor, $args) { 
      $o= unserialize('O:'.strlen($class).':"'.$class.'":0:{}');
      call_user_func_array(array($o, $ctor), $args);
      return $o; 
    }

    public static function create(lang·Object $o) { 
      return $o; 
    }

    public static function instance($class, $args, $bytes) { 
      static $c= 0;
      
      $name= $class.($c++);
      eval('class '.$name.(interface_exists($class) ? ' extends lang·Object implements ' : ' extends ').$class.$bytes);

      $class= new ReflectionClass($name);
      return $class->getConstructor() ? $class->newInstanceArgs($args) : $class->newInstance();
    }
    
    public static function handleexception($e) {
      if ($e instanceof XPException && $e->cause instanceof lang·SystemExit) {
        exit($e->cause->message);
      }
      echo 'Unhandled ', $e;
    }
  }
  
  function with() { }

  // {{{ internal void __error(int code, string msg, string file, int line)
  //     Error callback
  function __error($code, $msg, $file, $line) {
    if (0 == error_reporting() || is_null($file)) return;

    $errors= xp::registry('errors');
    @$errors[$file][$line][$msg]++;
    xp::registry('errors', $errors);
  }
  // }}}
  
  // {{{ Init
  set_error_handler('__error');
  error_reporting(E_ALL);
  xp::$null= new null();
  xp::registry('errors', array());
  XPException::$instance= new XPException();
  set_exception_handler(array('xp', 'handleexception'));
  
  define('LONG_MAX', is_int(2147483648) ? 9223372036854775807 : 2147483647);
  define('LONG_MIN', -LONG_MAX - 1);
  
  // }}}
  
  // {{{ Builtin classes
  uses(
    'lang.Error',
    'lang.XPException',
    'lang.XPClass',
    'lang.NullPointerException',
    'lang.IllegalAccessException',
    'lang.IllegalArgumentException',
    'lang.IllegalStateException',
    'lang.FormatException',
    'lang.ClassLoader',
    'lang.SystemExit'
  );
  
  class lang·Object {
    public $__id;

    public function hashCode() {
      if (!isset($this->__id)) {
        $this->__id= microtime();
      }
      return $this->__id;
    }
 
    public function equals($cmp) {
      return $this === $cmp;
    }
  
    public function getClassName() {
      return xp::nameOf(get_class($this));
    }

    public function getClass() {
      $c= new lang·XPClass($this);
      return $c;
    }
 
    public function toString() {
      return xp::stringOf($this);
    }

    public function __toString() {
      return $this->toString();
    }
  }
  // }}}
?>
