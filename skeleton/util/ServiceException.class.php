<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.ChainedException');

  /**
   * Indicates a certain fault occurred. Service methods may throw
   * this exception to indicate a well-known, categorised exceptional
   * situation has been met.
   *
   * The faultcode set within this exception object will be propagated into
   * the server's fault message's faultcode. This code can be used by clients
   * to recognize the type of error (other than by looking at the message).
   *
   * @see      xp://webservices.soap.rpc.SoapRpcRouter#doPost
   * @purpose  Custom service exception.
   */
  class ServiceException extends ChainedException {
    public
      $faultcode;

    /**
     * Constructor
     *
     * @param   var faultcode faultcode (can be int or string)
     * @param   string message
     * @param   lang.Throwable default NULL cause causing exception
     */
    public function __construct($faultcode, $message, $cause= NULL) {
      $this->faultcode= $faultcode;
      parent::__construct($message, $cause);
    }

    /**
     * Set Faultcode
     *
     * @param   var faultcode
     */
    public function setFaultcode($faultcode) {
      $this->faultcode= $faultcode;
    }

    /**
     * Get Faultcode
     *
     * @return  var
     */
    public function getFaultcode() {
      return $this->faultcode;
    }
    
    /**
     * Retrieve stacktrace from cause if set or from self otherwise.
     *
     * @return  lang.StackTraceElement[] array of stack trace elements
     * @see     xp://lang.StackTraceElement
     */
    public function getStackTrace() {
      if (NULL !== $this->cause) return $this->cause->getStackTrace();
      
      return parent::getStackTrace();
    }
  }
?>
