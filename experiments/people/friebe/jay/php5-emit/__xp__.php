<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$ 
 */
  error_reporting(E_ALL);
  
  define('SKELETON_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'skeleton'.DIRECTORY_SEPARATOR);
  ini_set('include_path', '.'.PATH_SEPARATOR.SKELETON_PATH);

  class XPException extends Exception {
    public $cause= NULL;
    public static $instance= NULL;

    function __toString() { 
      return $this->cause->toString(); 
    }
  }
  
  function uses() {
    foreach (func_get_args() as $class) {
      $file= strtr($class, '.', DIRECTORY_SEPARATOR);
      
      foreach (explode(PATH_SEPARATOR, ini_get('include_path')) as $path) {
        if (file_exists($path.DIRECTORY_SEPARATOR.$file.'.php5')) {
          if (filemtime($path.DIRECTORY_SEPARATOR.$file.'.php5') < filemtime($path.DIRECTORY_SEPARATOR.$file.'.xp')) {
            echo '*** PHP5 Version older than XP... ';
            break;
          } else {
            // echo '*** Loading ', $file, "\n";
            require_once($file.'.php5');
            continue 2;
          }
        }
      }
      
      // Could not find the file, compile
      $cmd= sprintf(getenv('COMPILE_CMD'), SKELETON_PATH.$file.'.xp');
      echo '*** Compiling ', $file, ' using (', $cmd, ")\n";
      passthru($cmd);
      require_once($file.'.php5');
    }
  }
  
  
  final class null {
    public function __set($prop, $value) { 
      throw xp::exception(new xp·lang·NullPointerException('Set: '.$prop));
    }

    public function __get($prop) { 
      throw xp::exception(new xp·lang·NullPointerException('Get: '.$prop));
    }

    public function __call($method, $args) { 
      throw xp::exception(new xp·lang·NullPointerException('Invoke: '.$method));
    }
    
    public function __toString() {
      return '<null>';
    }
  }
  
  function is($name, $object) { 
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

    public static function stringOf($expr) {
      if ($expr instanceof xp·lang·Object) {
        return $expr->toString();
      }
      return var_export($expr, TRUE);
    }

    public static function typeOf($expr) {
      if ($expr instanceof xp·lang·Object) {
        return $expr->getClassName();
      }
      return gettype($expr);
    }

    public static function registry($key= NULL, $value= NULL) {
      switch (func_num_args()) {
        case 0: return xp::$registry;
        case 1: return xp::$registry[$key];
        case 2: xp::$registry[$key]= $value;
      }
    }

    public static function exception(xp·lang·Throwable $e) {
      XPException::$instance->cause= $e;
      return XPException::$instance;
    }

    public static function spawn($class, $ctor, $args) { 
      $o= unserialize('O:'.strlen($class).':"'.$class.'":0:{}');
      call_user_func_array(array($o, $ctor), $args);
      return $o; 
    }

    public static function create(xp·lang·Object $o) { 
      return $o; 
    }

    public static function instance($class, $args, $bytes) { 
      static $c= 0;
      
      $name= $class.($c++);
      eval('class '.$name.(interface_exists($class) ? ' extends xp·lang·Object implements ' : ' extends ').$class.$bytes);

      $c= new ReflectionClass($name);
      return $c->getConstructor() ? $c->newInstanceArgs($args) : $c->newInstance();
    }
    
    public static function handleexception($e) {
      if ($e instanceof XPException && $e->cause instanceof xp·lang·SystemExit) {
        exit($e->cause->message);
      }
      echo $e;
    }
  }

  // {{{ internal void __error(int code, string msg, string file, int line)
  //     Error callback
  function __error($code, $msg, $file, $line) {
    if (0 == error_reporting() || is_null($file)) return;

    $errors= &xp::registry('errors');
    @$errors[$file][$line][$msg]++;
    xp::registry('errors', $errors);
  }
  // }}}
  
  // {{{ Init
  set_error_handler('__error');
  error_reporting(E_ALL | E_STRICT);
  xp::$null= new null();
  xp::registry('errors', array());
  XPException::$instance= new XPException();
  set_exception_handler(array('xp', 'handleexception'));
  // }}}
  
  // {{{ Builtin classes
  uses(
    'xp.lang.Error',
    'xp.lang.Exception',
    'xp.lang.XPClass',
    'xp.lang.NullPointerException',
    'xp.lang.IllegalAccessException',
    'xp.lang.IllegalArgumentException',
    'xp.lang.IllegalStateException',
    'xp.lang.FormatException',
    'xp.lang.ClassLoader',
    'xp.lang.SystemExit'
  );
  
  class xp·lang·Object {
    public $__id;

    public function hashCode() {
      if (!isset($this->__id)) {
        $this->__id= microtime();
      }
      return $this->__id;
    }
 
    public function equals(xp·lang·Object $cmp) {
      return $this === $cmp;
    }
  
    public function getClassName() {
      return xp::nameOf(get_class($this));
    }

    public function getClass() {
      $c= new xp·lang·XPClass($this);
      return $c;
    }
 
     public function toString() {
      return $this->getClassName().'@'.var_export($this, 1);
    }
  }
  // }}}
?>
