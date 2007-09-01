<?php
/* This class is part of the XP framework
 *
 * $Id: EascInitMessage.class.php 9302 2007-01-16 17:01:53Z kiesel $ 
 */

  namespace remote::server::message;

  uses('remote.server.message.EascMessage');

  /**
   * EASC Init message
   *
   * @purpose  Init message
   */
  class EascInitMessage extends EascMessage {

    /**
     * Get type of message
     *
     * @return  int
     */
    public function getType() {
      return REMOTE_MSG_INIT;
    }
  
    /**
     * Handle message
     *
     * @param   remote.server.EASCProtocol protocol
     * @return  mixed data
     */
    public function handle($protocol, $data) {
      $this->setValue($b= TRUE);
    }
  }
?>
