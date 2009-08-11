<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('peer.SocketException');

  /**
   * Indicate a timeout occurred on a socket read
   *
   * @see      xp://peer.Socket#setTimeout
   * @see      xp://peer.SocketException
   */
  class SocketTimeoutException extends SocketException {

  }
?>
