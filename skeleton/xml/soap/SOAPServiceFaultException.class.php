<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.http.HttpConstants');

  /**
   * Indicates a certains SOAP fault occurred. Server methods may throw
   * this exception to indicate a well-known, categorised exceptional
   * situation has been met.
   *
   * The faultcode set within this exception object will be propagated into
   * the server's fault message's faultcode. This code can be used by clients
   * to recognize the type of error (other than by looking at the message).
   *
   * @see      xp://xml.soap.rpc.SoapRpcRouter#doPost
   * @purpose  SOAP service's custom exception.
   */
  class SOAPServiceFaultException extends Exception {
    var
      $faultcode=   HTTP_INTERNAL_SERVER_ERROR;

    /**
     * Constructor
     *
     * @access  public
     * @param   int faultcode
     * @param   string message
     */
    function __construct($faultcode, $message) {
      parent::__construct('Fault #'.$faultcode.'; '.$message);
    }
  }
?>
