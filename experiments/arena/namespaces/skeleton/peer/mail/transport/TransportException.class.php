<?php
/* This class is part of the XP framework
 *
 * $Id: TransportException.class.php 10977 2007-08-27 17:14:26Z friebe $ 
 */

  namespace peer::mail::transport;

  ::uses('lang.ChainedException');

  /**
   * TransportException
   *
   * @see      xp://peer.mail.transport.Transport
   * @purpose  Indicate a transport error has occured
   */
  class TransportException extends lang::ChainedException {
  }
?>
