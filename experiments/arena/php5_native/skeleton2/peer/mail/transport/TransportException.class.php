<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.ChainedException');

  /**
   * TransportException
   *
   * @see      xp://peer.mail.transport.Transport
   * @purpose  Indicate a transport error has occured
   */
  class TransportException extends ChainedException {
  }
?>
