<?php
/* This class is part of the XP framework
 *
 * $Id: UnknownProtocolException.class.php 7193 2006-06-27 09:28:09Z friebe $
 */

  namespace remote::protocol;

  /**
   * Indicates a protocol passed to the handler factory is unknown.
   *
   * @see      xp://remote.HandlerFactory
   * @purpose  Exception
   */
  class UnknownProtocolException extends lang::IllegalArgumentException {

  }
?>
