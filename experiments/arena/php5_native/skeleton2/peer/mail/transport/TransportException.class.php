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
  class TransportException extends XPException {
    public
      $cause = NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string message
     * @param   &lang.Exception cause
     */
    public function __construct($message, &$cause) {
      $this->cause= &$cause;
      parent::__construct($message);
    }
  
    /**
     * Get string representation
     *
     * @access  public
     * @return  string
     */
    public function toString() {
      return parent::toString().(->causeis('XPException', $this) 
        ? '  [caused by '.$this->cause->getClassName()."\n  (".$this->cause->message.")\n  ]"
        : '  [no cause]'
      );
    }
  }
?>
