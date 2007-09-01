<?php
/* This class is part of the XP framework
 *
 * $Id: SocketException.class.php 2167 2003-09-01 22:18:38Z friebe $ 
 */

  namespace peer;

  ::uses('io.IOException');

  /**
   * Indicate a generic I/O error on a socket
   *
   * @see      xp://io.IOException
   * @purpose  Exception
   */
  class SocketException extends io::IOException {

  }
?>
