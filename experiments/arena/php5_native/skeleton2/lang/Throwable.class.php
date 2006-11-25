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
   * @see      xp://lang.Exception
   * @test     xp://net.xp_framework.unittest.core.ExceptionsTest
   * @purpose  Base class
   */
  class Throwable extends Exception implements Generic {
    public 
      $message  = '',
      $trace    = array();

    /**
     * Constructor
     *
     * @access  public
     * @param   string message
     */
    public function __construct($message) {
      static $except= array(
        'call_user_func_array'  => 1, 
        'call_user_func'        => 1, 
        'object'                => 1
      );
      $this->__id= microtime();
      $this->message= $message;
      
      $errors= xp::registry('errors');
      foreach (debug_backtrace() as $no => $trace) {
        if (!isset($trace['function']) || isset($except[$trace['function']])) continue;

        // Pop error messages off the copied error stack
        if (isset($trace['file']) && isset($errors[$trace['file']])) {
          $messages= $errors[$trace['file']];
          unset($errors[$trace['file']]);
        } else {
          $messages= array(array('' => 1));
        }

        // Not all of these are always set: debug_backtrace() should
        // initialize these - at least - to NULL, IMO => Workaround.
        $this->addStackTraceFor(
          isset($trace['file']) ? $trace['file'] : NULL,
          isset($trace['class']) ? $trace['class'] : NULL,
          isset($trace['function']) ? $trace['function'] : NULL,
          isset($trace['line']) ? $trace['line'] : NULL,
          isset($trace['args']) ? $trace['args'] : NULL,
          $messages
        );
      }
      
      // Remaining error messages
      foreach ($errors as $file => $list) {
        $class= ('.class.php' == substr($file, -10)
          ? substr(basename($file), 0, -10)
          : '<main>'
        );
        
        $this->addStackTraceFor(
          $file,
          $class,
          NULL,
          NULL,
          array(),
          $list
        );
      }
      
      // Store remaining errors back to xp::registry()
      xp::registry('errors', $errors);
    }
    
    /**
     * Adds new stacktrace elements to the internal list of stacktrace
     * elements, each for one error.
     *
     * @access  protected
     * @param   string file
     * @param   string class
     * @param   string function
     * @param   int originalline
     * @param   mixed[] args
     * @param   mixed[] errors
     */
    public function addStackTraceFor($file, $class, $function, $originalline, $args, $errors) {
      foreach ($errors as $line => $errormsg) {
        foreach ($errormsg as $message => $amount) {
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
     * @access  public
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
     * @access  public
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
     * @access  public
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
     * @access  public
     * @return  string
     */
    public function toString() {
      $s= $this->compoundMessage()."\n";
      for ($i= 0, $t= sizeof($this->trace); $i < $t; $i++) {
        $s.= $this->trace[$i]->toString(); 
      }
      return $s;
    }

    /**
     * Returns a hashcode for this object
     *
     * @access  public
     * @return  string
     */
    function hashCode() {
      return $this->__id;
    }
    
    /**
     * Indicates whether some other object is "equal to" this one.
     *
     * @access  public
     * @param   &lang.Object cmp
     * @return  bool TRUE if the compared object is equal to this object
     */
    function equals(&$cmp) {
      return $this === $cmp;
    }
    
    /** 
     * Returns the fully qualified class name for this class 
     * (e.g. "io.File")
     * 
     * @access  public
     * @return  string fully qualified class name
     */
    function getClassName() {
      return xp::nameOf(get_class($this));
    }

    /**
     * Returns the runtime class of an object.
     *
     * @access  public
     * @return  &lang.XPClass runtime class
     * @see     xp://lang.XPClass
     */
    function &getClass() {
      $c= &new XPClass($this);
      return $c;
    }
  }
?>
