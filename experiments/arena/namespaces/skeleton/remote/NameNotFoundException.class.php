<?php
/* This class is part of the XP framework
 *
 * $Id: NameNotFoundException.class.php 7214 2006-06-28 08:15:16Z friebe $ 
 */

  namespace remote;

  uses('remote.RemoteException');

  /**
   * Indicates a name could not be found during lookup
   *
   * @see      xp://remote.Remote
   * @purpose  Exception
   */
  class NameNotFoundException extends RemoteException {

  }
?>
