<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.StackTraceElement');

  /**
   * Throwable
   *
   * @see      xp://lang.Error
   * @see      xp://lang.XPException
   * @purpose  Base class
   */
  class Throwable extends Exception implements Generic {
    protected 
      $_trace    = array();

    protected 
      $__id      = NULL;

    /**
     * Constructor
     *
     * @access  public
     * @param   string message
     */
    public function __construct($message) {
      static $except= array(
        'call_user_func_array', 'call_user_func', 'object', '__call', '__set', '__get'
      );

      parent::__construct($message);
      foreach ($this->getTrace() as $trace) {
        if (!isset($trace['function']) || in_array($trace['function'], $except)) continue;

        // Pop error messages off the copied error stack
        if (isset($trace['file']) && isset(xp::$errors[$trace['file']])) {
          $messages= xp::$errors[$trace['file']];
          unset(xp::$errors[$trace['file']]);
        } else {
          $messages= array();
        }

        // Not all of these are always set: debug_backtrace() should
        // initialize these - at least - to NULL, IMO => Workaround.
        $this->_trace[]= new StackTraceElement(
          isset($trace['file']) ? $trace['file'] : NULL,
          isset($trace['class']) ? $trace['class'] : NULL,
          isset($trace['function']) ? $trace['function'] : NULL,
          isset($trace['line']) ? $trace['line'] : NULL,
          isset($trace['args']) ? $trace['args'] : NULL,
          $messages
        );
      }
      
      // Remaining error messages
      foreach (array_keys(xp::$errors) as $key) {
        $class= ('.class.php' == substr($key, -10)
          ? strtolower(substr(basename($key), 0, -10))
          : '<main>'
        );
        for ($i= 0, $s= sizeof(xp::$errors[$key]); $i < $s; $i++) { 
          $this->_trace[]= new StackTraceElement(
            $key,
            $class,
            NULL,
            xp::$errors[$key][$i][2],
            array(),
            array(xp::$errors[$key][$i])
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
      return $this->_trace;
    }
    
    /**
     * Print "stacktrace" to standard error
     *
     * @see     xp://lang.Throwable#toString
     * @param   resource fd default STDERR
     * @access  public
     */
    public function printStackTrace($fd= STDERR) {
      fputs($fd, self::toString());
    }
 
    /**
     * Return formatted output of stacktrace
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
     * @access  public
     * @return  string
     */
    public function toString() {
      $s= sprintf(
        "Exception %s (%s)\n",
        self::getClassName(),
        $this->message
      );
      for ($i= 0, $t= sizeof($this->_trace); $i < $t; $i++) {
        $s.= $this->_trace[$i]->toString();
      }
      return $s;
    }
    
    /**
     * Returns a hashcode for this object
     *
     * @access  public
     * @return  string
     */
    public function hashCode() {
      if (!$this->__id) $this->__id= microtime();
      return $this->__id;
    }
    
    /**
     * Indicates whether some other object is "equal to" this one.
     *
     * @access  public
     * @param   &lang.Generic cmp
     * @return  bool TRUE if the compared object is equal to this object
     */
    public function equals(Generic $cmp) {
      return $this === $cmp;
    }
    
    /** 
     * Returns the fully qualified class name for this class 
     * (e.g. "io.File")
     *
     * This is a shorthand for the following:
     * <code>
     *   $name= $instance->getClass()->getName();
     * </code>
     * 
     * @model   final
     * @access  public
     * @return  string fully qualified class name
     */
    public final function getClassName() {
      return xp::nameOf(get_class($this));
    }

    /**
     * Returns the runtime class of an object.
     *
     * @model   final
     * @access  public
     * @return  &lang.XPClass runtime class
     * @see     xp://lang.XPClass
     */
    public final function getClass() {
      return XPClass::forInstance($this);
    }
    
    /**
     * Wrapper for PHP's builtin cast mechanism
     *
     * @see     xp://lang.Object#toString
     * @access  public
     * @return  string
     */
    public function __toString() {
      return self::toString();
    }
  }
?>
