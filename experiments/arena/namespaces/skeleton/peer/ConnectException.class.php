<?php
/* This class is part of the XP framework
 *
 * $Id: ConnectException.class.php 2167 2003-09-01 22:18:38Z friebe $ 
 */

  namespace peer;

  ::uses('peer.SocketException');

  /**
   * Indicate an error occured during connect
   *
   * @see      xp://io.IOException
   * @purpose  Exception
   */
  class ConnectException extends SocketException {

  }
?>
