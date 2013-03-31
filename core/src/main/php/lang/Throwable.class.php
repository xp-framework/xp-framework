<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.Generic', 'lang.StackTraceElement');

  /**
   * Throwable
   *
   * @see      xp://lang.Error
   * @see      xp://lang.XPException
   * @see      http://mindprod.com/jgloss/chainedexceptions.html
   * @see      http://www.jguru.com/faq/view.jsp?EID=1026405  
   * @test     xp://net.xp_framework.unittest.core.ExceptionsTest
   * @test     xp://net.xp_framework.unittest.core.ChainedExceptionTest
   * @purpose  Base class
   */
  class Throwable extends Exception implements Generic {
    public
      $__id;

    public 
      $cause    = NULL,
      $message  = '',
      $trace    = array();
    
    static function __static() {
    
      // Workaround for missing detail information about return types in
      // builtin classes.
      xp::$meta['php.Exception']= array(
        'class' => array(4 => NULL, array()),
        0 => array(),
        1 => array(
          'getMessage'       => array(1 => array(), 'string', array(), NULL, array()),
          'getCode'          => array(1 => array(), 'int', array(), NULL, array()),
          'getFile'          => array(1 => array(), 'string', array(), NULL, array()),
          'getLine'          => array(1 => array(), 'int', array(), NULL, array()),
          'getTrace'         => array(1 => array(), 'var[]', array(), NULL, array()),
          'getPrevious'      => array(1 => array(), 'lang.Throwable', array(), NULL, array()),
          'getTraceAsString' => array(1 => array(), 'string', array(), NULL, array()),
        )
      );
    }

    /**
     * Constructor
     *
     * @param   string message
     */
    public function __construct($message, $cause= NULL) {
      static $u= 0;
      $this->__id= uniqid('', TRUE);
      $this->message= is_string($message) ? $message : xp::stringOf($message);
      $this->cause= $cause;
      $this->fillInStackTrace();
    }

    /**
     * Static method handler
     *
     */
    public static function __callStatic($name, $args) {
      $self= get_called_class();
      if ("\7" === $name{0}) {
        return call_user_func_array(array($self, substr($name, 1)), $args);
      }
      throw new Error('Call to undefined method '.$self.'::'.$name);
    }

    /**
     * Field read handler
     *
     */
    public function __get($name) {
      return NULL;
    }

    /**
     * Field write handler
     *
     */
    public function __set($name, $value) {
      $this->{$name}= $value;
    }
    
    /**
     * Method handler
     *
     */
    public function __call($name, $args) {
      if ("\7" === $name{0}) {
        return call_user_func_array(array($this, substr($name, 1)), $args);
      }

      $t= debug_backtrace();

      // Get self
      $i= 1; $s= sizeof($t);
      while (!isset($t[$i]['class']) && $i++ < $s) { }
      $self= $t[$i]['class'];

      // Get scope
      $i++;
      while (!isset($t[$i]['class']) && $i++ < $s) { }
      $scope= isset($t[$i]['class']) ? $t[$i]['class'] : NULL;

      if (NULL != $scope && isset(xp::$ext[$scope])) {
        foreach (xp::$ext[$scope] as $type => $class) {
          if (!$this instanceof $type || !method_exists($class, $name)) continue;
          array_unshift($args, $this);
          return call_user_func_array(array($class, $name), $args);
        }
      }
      throw new Error('Call to undefined method '.xp::nameOf($self).'::'.$name.'() from scope '.xp::nameOf($scope));
    }

    /**
     * Set cause
     *
     * @param   lang.Throwable cause
     */
    public function setCause($cause) {
      $this->cause= $cause;
    }

    /**
     * Get cause
     *
     * @return  lang.Throwable
     */
    public function getCause() {
      return $this->cause;
    }
    
    /**
     * Fills in stack trace information. 
     *
     * @return  lang.Throwable this
     */
    public function fillInStackTrace() {
      static $except= array(
        'call_user_func_array'  => 1, 
        'call_user_func'        => 1
      );

      // Error messages
      foreach (xp::$errors as $file => $list) {
        $this->addStackTraceFor($file, NULL, NULL, NULL, array(), $list);
      }

      foreach (debug_backtrace() as $i => $trace) {
        if (
          !isset($trace['function']) || 
          isset($except[$trace['function']]) ||
          (isset($trace['object']) && $trace['object'] instanceof self)
        ) continue;

        // Not all of these are always set: debug_backtrace() should
        // initialize these - at least - to NULL, IMO => Workaround.
        $this->addStackTraceFor(
          isset($trace['file']) ? $trace['file'] : NULL,
          isset($trace['class']) ? $trace['class'] : NULL,
          isset($trace['function']) ? $trace['function'] : NULL,
          isset($trace['line']) ? $trace['line'] : NULL,
          isset($trace['args']) ? $trace['args'] : NULL,
          array(array('' => 1))
        );
      }
      return $this;
    }
    
    /**
     * Adds new stacktrace elements to the internal list of stacktrace
     * elements, each for one error.
     *
     * @param   string file
     * @param   string class
     * @param   string function
     * @param   int originalline
     * @param   var[] args
     * @param   var[] errors
     */
    protected function addStackTraceFor($file, $class, $function, $originalline, $args, $errors) {
      foreach ($errors as $line => $errormsg) {
        foreach ($errormsg as $message => $details) {
          if (is_array($details)) {
            $class= $details['class'];
            $function= $details['method'];
            $amount= $details['cnt'];
          } else {
            $amount= $details;
          }
          
          $this->trace[]= new StackTraceElement(
            $file,
            $class,
            $function,
            $originalline ? $originalline : $line,
            $args,
            $message.($amount > 1 ? ' (... '.($amount - 1).' more)' : '')
          );
        }
      }
    }

    /**
     * Return an array of stack trace elements
     *
     * @return  lang.StackTraceElement[] array of stack trace elements
     * @see     xp://lang.StackTraceElement
     */
    public function getStackTrace() {
      return $this->trace;
    }

    /**
     * Print "stacktrace" to standard error
     *
     * @see     xp://lang.Throwable#toString
     * @param   resource fd default STDERR
     */
    public function printStackTrace($fd= STDERR) {
      fputs($fd, $this->toString());
    }

    /**
     * Return compound message of this exception. In this default 
     * implementation, returns the following:
     *
     * <pre>
     *   Exception [FULLY-QUALIFIED-CLASSNAME] ([MESSAGE])
     * </pre>
     *
     * May be overriden by subclasses
     *
     * @return  string
     */
    public function compoundMessage() {
      return sprintf(
        'Exception %s (%s)',
        $this->getClassName(),
        $this->message
      );
    }
 
    /**
     * Return compound message followed by the formatted output of this
     * exception's stacktrace.
     *
     * Example:
     * <pre>
     * Exception lang.ClassNotFoundException (class "" [] not found)
     *   at lang.ClassNotFoundException::__construct((0x15)'class "" [] not found') \
     *   [line 79 of StackTraceElement.class.php] 
     *   at lang.ClassLoader::loadclass(NULL) [line 143 of XPClass.class.php] 
     *   at lang.XPClass::forname(NULL) [line 6 of base_test.php] \
     *   Undefined variable:  nam
     * </pre>
     *
     * Usually not overridden by subclasses unless stacktrace format 
     * should differ - otherwise overwrite compoundMessage() instead!.
     *
     * @return  string
     */
    public function toString() {
      $s= $this->compoundMessage()."\n";
      $tt= $this->getStackTrace();
      $t= sizeof($tt);
      for ($i= 0; $i < $t; $i++) {
        $s.= $tt[$i]->toString(); 
      }
      if (!$this->cause) return $s;
      
      $loop= $this->cause;
      while ($loop) {

        // String of cause
        $s.= 'Caused by '.$loop->compoundMessage()."\n";

        // Find common stack trace elements
        $lt= $loop->getStackTrace();
        for ($ct= $cc= sizeof($lt)- 1, $t= sizeof($tt)- 1; $ct > 0 && $cc > 0 && $t > 0; $cc--, $t--) {
          if (!$lt[$cc]->equals($tt[$t])) break;
        }

        // Output uncommon elements only and one line how many common elements exist!
        for ($i= 0; $i < $cc; $i++) {
          $s.= xp::stringOf($lt[$i]); 
        }
        if ($cc != $ct) $s.= '  ... '.($ct - $cc + 1)." more\n";
        
        $loop= $loop->cause;
        $tt= $lt;
      }
      
      return $s;
    }

    /**
     * Returns a hashcode for this object
     *
     * @return  string
     */
    public function hashCode() {
      return $this->__id;
    }
    
    /**
     * Indicates whether some other object is "equal to" this one.
     *
     * @param   lang.Generic cmp
     * @return  bool TRUE if the compared object is equal to this object
     */
    public function equals($cmp) {
      if (!$cmp instanceof Generic) return FALSE;
      if (!$cmp->__id) $cmp->__id= uniqid('', TRUE);
      return $this === $cmp;
    }
    
    /** 
     * Returns the fully qualified class name for this class 
     * (e.g. "io.File")
     * 
     * @return  string fully qualified class name
     */
    public function getClassName() {
      return xp::nameOf(get_class($this));
    }

    /**
     * Returns the runtime class of an object.
     *
     * @return  lang.XPClass runtime class
     * @see     xp://lang.XPClass
     */
    public function getClass() {
      return new XPClass($this);
    }
  }
?>
