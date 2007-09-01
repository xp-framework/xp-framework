<?php
/* This class is part of the XP framework
 *
 * $Id: RemoteException.class.php 10977 2007-08-27 17:14:26Z friebe $ 
 */

  namespace remote;

  uses('lang.ChainedException');

  /**
   * Indicates an exception occured on the server
   *
   * @see      xp://Remote
   * @purpose  Exception
   */
  class RemoteException extends lang::ChainedException {

  }
?>
