<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$ 
 */
  error_reporting(E_ALL);

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
            // echo '*** PHP5 Version older than XP... ';
          } else {
            // echo '*** Loading ', $file, "\n";
            require_once($file.'.php5');
            continue 2;
          }
        }
      }
      
      // Could not find the file, compile
      $cmd= 'php tophp5.php /home/thekid/devel/xp2/'.$file.'.xp';
      // echo '*** Compiling ', $cmd, "\n";
      passthru($cmd);
      require_once($file.'.php5');
    }
  }
  
  ini_set('include_path', '.:/home/thekid/devel/xp2/');
  
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
    
    public static function reflect($n) {
      return $n;
    }

    public static function nameOf($name) {
      return strtr($name, '·', '.');
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
  
  // {{{ Init
  xp::$null= new null();
  xp::registry('errors', array());
  XPException::$instance= new XPException();
  set_exception_handler(array('xp', 'handleexception'));
  // }}}
  
  // {{{ Builtin classes
  uses(
    'xp.lang.Object',
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
  // }}}
?>
