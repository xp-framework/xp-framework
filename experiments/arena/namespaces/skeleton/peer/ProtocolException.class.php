<?php
/* This class is part of the XP framework
 *
 * $Id: ProtocolException.class.php 3021 2004-02-23 17:57:39Z friebe $ 
 */

  namespace peer;

  ::uses('peer.SocketException');

  /**
   * Indicate an error was detected in the protocol
   *
   * @see      xp://peer.SocketException
   * @purpose  Exception
   */
  class ProtocolException extends SocketException {

  }
?>
