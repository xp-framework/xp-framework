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
   * @see      xp://lang.Exception
   * @purpose  Base class
   */
  class Throwable extends Object {
    var 
      $message  = '',
      $trace    = array();

    /**
     * Constructor
     *
     * @access  public
     * @param   string message
     */
    function __construct($message) {
      static $except= array(
        'call_user_func_array'  => 1, 
        'call_user_func'        => 1, 
        'object'                => 1
      );
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
          ? strtolower(substr(basename($file), 0, -10))
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
    function addStackTraceFor($file, $class, $function, $originalline, $args, $errors) {
      foreach ($errors as $line => $errormsg) {
        foreach ($errormsg as $message => $amount) {
          $this->trace[]= &new StackTraceElement(
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
     * Get Message
     *
     * @access  public
     * @return  string
     */
    function getMessage() {
      return $this->message;
    }

    /**
     * Return an array of stack trace elements
     *
     * @access  public
     * @return  lang.StackTraceElement[] array of stack trace elements
     * @see     xp://lang.StackTraceElement
     */
    function getStackTrace() {
      return $this->trace;
    }

    /**
     * Print "stacktrace" to standard error
     *
     * @see     xp://lang.Throwable#toString
     * @param   resource fd default STDERR
     * @access  public
     */
    function printStackTrace($fd= STDERR) {
      fputs($fd, $this->toString());
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
    function toString() {
      $s= sprintf(
        "Exception %s (%s)\n",
        $this->getClassName(),
        $this->message
      );
      for ($i= 0, $t= sizeof($this->trace); $i < $t; $i++) {
        $s.= $this->trace[$i]->toString(); 
      }
      return $s;
    }
  }
?>
