<?php
/* This class is part of the XP framework
 *
 * $Id: InvocationException.class.php 6203 2005-12-02 12:33:24Z friebe $ 
 */

  namespace remote;

  uses('remote.RemoteException');

  /**
   * An exception occured in the remote server during invocation of a 
   * method
   *
   * @see      xp://remote.RemoteException
   * @purpose  Remote exception
   */
  class InvocationException extends RemoteException {
  
  }
?>
