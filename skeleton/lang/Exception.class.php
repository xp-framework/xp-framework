<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  /**
   * Kapselt die Exception
   */
  class Exception extends Object {
    var 
      $message;
     
    /**
     * Constructor
     */
    function __construct($message) {
      $this->message= $message;
      parent::__construct();
    }
    
    /**
     * "Stack Trace" ausgeben
     */
    function printStackTrace() {
      echo $this->getStackTrace();
    }
    
    /**
     * "Stack Trace" zurückgeben
     *
     * @return  string der StackTrace, vorformatiert
     */
    function getStackTrace() {
      $return= sprintf(
        "Exception %s (%s)\n",
        $this->getClassName(),
        $this->message
      );
      
      // Pfusch, aber in PHP4 nicht anders möglich...
      for ($i= 0; $i< sizeof($GLOBALS['php_errorcode']); $i++) {
      
        // Methoden/Funktionsnamen raussuchen
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
