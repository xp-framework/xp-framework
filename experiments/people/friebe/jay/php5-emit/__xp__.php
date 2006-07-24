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
  }

  final class xp {
    public static $null;

    public static function exception(xp·lang·Throwable $e) {
      XPException::$instance->cause= $e;
      return XPException::$instance;
    }

    public static function spawn($class, $ctor, $args) { 
      $o= unserialize('O:'.strlen($class).':"'.$class.'":0:{}');
      call_user_func_array(array($o, $ctor), $args);
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
  XPException::$instance= new XPException();
  set_exception_handler(array('xp', 'handleexception'));
  // }}}
  
  // {{{ Builtin classes
  class xp·lang·Object {
  
    function toString() {
      return $this->getClassName().'@('.var_export($this, TRUE).')';
    }
    
    function getClassName() {
      return strtr(get_class($this), '·', '~');
    }
  }

  class xp·lang·Throwable extends xp·lang·Object {
    public $message;
    public $trace;
    
    public function __construct($message) {
      $this->message= $message;
      $this->trace= debug_backtrace();
    }
    
    function toString() {
      $trace= '';
      for ($i= 1, $s= sizeof($this->trace); $i < $s; $i++) {
        $trace.= sprintf(
          "  at %s%s({%d arg(s)}) (%s:%d)\n",
          isset($this->trace[$i]['type']) ? ('->' == $this->trace[$i]['type'] 
            ? get_class($this->trace[$i]['object']).'->'
            : $this->trace[$i]['class'].'::'
          ) : '<main>::',
          $this->trace[$i]['function'],
          sizeof($this->trace[$i]['args']),   // TBI: String representation!
          basename(@$this->trace[$i]['file']),
          @$this->trace[$i]['line']
        );
      }
      return $this->getClassName().'@("'.$this->message."\") {\n".$trace.'}';
    }
  }

  class xp·lang·Exception extends xp·lang·Throwable { }
  class xp·lang·SystemExit extends xp·lang·Throwable { 
    public function __construct($message= 0) {
      parent::__construct($message);
    }
  }
  class xp·lang·IllegalAccessException extends xp·lang·Exception { }
  class xp·lang·NullPointerException extends xp·lang·Exception { }
  class xp·io·IOException extends xp·lang·Exception { }
  // }}}
?>
