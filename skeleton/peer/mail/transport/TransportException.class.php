<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * TransportException
   *
   * @see      xp://peer.mail.transport.Transport
   * @purpose  Indicate a transport error has occured
   */
  class TransportException extends Exception {
    var
      $cause = NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string message
     * @param   &lang.Exception cause
     */
    function __construct($message, &$cause) {
      $this->cause= &$cause;
      parent::__construct($message);
    }
  
    /**
     * Return a formatted representation of the "stracktrace"
     *
     * @access  public
     * @return  string
     */
    function getStackTrace() {
      return parent::getStackTrace().(is_a($this->cause, 'Exception') 
        ? '  [caused by '.$this->cause->getClassName()."\n  (".$this->cause->message.")\n  ]"
        : '  [no cause]'
      );
    }
  }
?>
