<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rmi.server.RemoteException');

  /**
   * A NoSuchObjectException is thrown if an attempt is made to invoke a 
   * method on an object that no longer exists in the server.
   *
   * @purpose  Exception
   */
  class NoSuchObjectException extends RemoteException {

  }
?>
