<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.SocketException');

  /**
   * Indicate an error occured during connect
   *
   * @see      xp://io.IOException
   * @purpose  Exception
   */
  class ConnectException extends SocketException {

  }
?>
