<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  /**
   * Exception
   *
   * @purpose  Base class for all other exceptions
   * @see      http://java.sun.com/docs/books/tutorial/essential/exceptions/definition.html
   */
  class Exception extends Object {
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
      $this->message= $message;
      if (function_exists('debug_backtrace')) $this->trace= debug_backtrace();
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
     * Print "stacktrace" to standard output
     *
     * @access  public
     */
    function printStackTrace() {
      echo $this->getStackTrace();
    }
    
    /**
     * Return a string representation of this exception
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return sprintf(
        "Exception %s (%s)\n",
        $this->getClassName(),
        $this->message
      );
    }
    
    /**
     * Return a formatted representation of the "stracktrace"
     *
     * @access  public
     * @return  string
     */
    function getStackTrace() {
      $return= sprintf(
        "Exception %s (%s)\n",
        $this->getClassName(),
        $this->message
      );
      
      // This is ugly...
      for ($i= 0, $s= sizeof($GLOBALS['php_errorcode']); $i < $s; $i++) {
        $func= '<main>';
        if ($fd= @fopen($GLOBALS['php_errorfile'][$i], 'r')) {

          $no= 0;
          while (
            (FALSE !== ($line= @fgets($fd, 4096))) &&
            (++$no <= $GLOBALS['php_errorline'][$i])
          ) {
            if (preg_match('/function\s+([^\r\n\s\t\(]+)/i', $line, $regs)) $func= $regs[1];
          }
          fclose($fd);
        }
        
        $return.= sprintf(
          "  at %s:%s (line %d:%s)\n",
          basename($GLOBALS['php_errorfile'][$i]),
          $func,
          $GLOBALS['php_errorline'][$i],
          $GLOBALS['php_errormessage'][$i]
        );
      }
      
      // This is for when debug_backtrace exists...
      for ($i= 0, $s= sizeof($this->trace); $i < $s; $i++) {
        $t= &$this->trace[$i];
        if ('call_user_func_array' == @$t['function']) continue;

        $class= '<main>';
        if (isset($t['class'])) $class= (isset($GLOBALS['php_class_names'][$t['class']]) 
          ? $GLOBALS['php_class_names'][$t['class']]
          : $t['class']
        );
        $args= array();
        for ($j= 0, $a= sizeof($t['args']); $j < $a; $j++) {
          if (is_array($t['args'][$j])) {
            $args[]= 'array ( ... )';
          } elseif (is_object($t['args'][$j])) {
            $args[]= get_class($t['args'][$j]).' { ... }';
          } else {
            $args[]= var_export($t['args'][$j], 1);
          }
        }
        
        $return.= sprintf(
          "  at %s:%s(%s) [line %d of class %s]\n",
          $class,
          isset($t['function']) ? $t['function'] : '<main>',
          implode(', ', $args),
          isset($t['line']) ? $t['line'] : __LINE__,
          basename(isset($t['file']) ? $t['file'] : __FILE__)
        );
      }
      
      return $return;
    }
  }
?>
