<?php
/* This class is part of the XP framework
 *
 * $Id: EascExceptionMessage.class.php 9303 2007-01-16 17:02:43Z kiesel $ 
 */

  namespace remote::server::message;

  uses('remote.server.message.EascMessage');

  /**
   * EASC exception message
   *
   * @purpose  Exception message
   */
  class EascExceptionMessage extends EascMessage {

    /**
     * Get type of message
     *
     * @return  int
     */
    public function getType() {
      return REMOTE_MSG_EXCEPTION;
    }
  }
?>
