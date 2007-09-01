<?php
/* This class is part of the XP framework
 *
 * $Id: EascLookupMessage.class.php 9302 2007-01-16 17:01:53Z kiesel $ 
 */

  namespace remote::server::message;
 
  uses(
    'remote.server.message.EascMessage',
    'remote.server.naming.NamingDirectory'
  );

  /**
   * EASC lookup message
   *
   * @purpose  Lookup message
   */
  class EascLookupMessage extends EascMessage {

    /**
     * Get type of message
     *
     * @return  int
     */
    public function getType() {
      return REMOTE_MSG_LOOKUP;
    }
    
    /**
     * Handle message
     *
     * @param   remote.server.EASCProtocol protocol
     * @return  mixed data
     */
    public function handle($protocol, $data) {
      $offset= 0;
      $name= $protocol->readString($data, $offset);

      $directory= remote::server::naming::NamingDirectory::getInstance();
      $this->setValue($directory->lookup($name));
    }
  }
?>
