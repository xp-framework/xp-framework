<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.StackTraceElement');

  /**
   * Throwable
   *
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
      static $except= array('call_user_func_array', 'call_user_func', 'object');
      $this->message= $message;
      
      $errors= &xp::registry('errors');
      foreach (debug_backtrace() as $trace) {
        if (!isset($trace['function']) || in_array($trace['function'], $except)) continue;

        $this->trace[]= &new StackTraceElement(
          $trace['file'],
          $trace['class'],
          $trace['function'],
          $trace['line'],
          $trace['args'],
          $errors[$trace['file']]
        );
      }

      parent::__construct();
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
     * @see     xp://lang.Exception#toString
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
