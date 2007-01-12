<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('remote.server.message.EascMessage');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
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
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function handle($protocol, $data) {
      $this->setValue($b= TRUE);
    }
  }
?>
