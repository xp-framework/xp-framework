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
      $message;
     
    /**
     * Constructor
     *
     * @access  public
     * @param   string message
     */
    function __construct($message) {
      $this->message= $message;
      parent::__construct();
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
      for ($i= 0; $i< sizeof($GLOBALS['php_errorcode']); $i++) {
        if ($fd= @fopen($GLOBALS['php_errorfile'][$i], 'r')) {
          $func= '<main>';
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
      return $return;
    }
  }
?>
