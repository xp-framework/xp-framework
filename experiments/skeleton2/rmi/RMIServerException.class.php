<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rmi.RMIException');

  /**
   * A ServerException is thrown as a result of a remote method call if 
   * the execution of the remote method on the server machine throws a 
   * RemoteException.
   *
   * @purpose  Exception
   */
  class RMIServerException extends RMIException {
    public
      $cause    = NULL;
  
    /**
     * Constructor
     *
     * @access  public
     * @param   string message
     * @param   &lang.Exception cause
     */
    public function __construct($message, XPException $cause) {
      $this->cause= $cause;
      parent::__construct($message);
    }
  }
?>
