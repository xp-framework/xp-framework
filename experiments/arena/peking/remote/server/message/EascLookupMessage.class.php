<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'remote.server.message.EascMessage',
    'remote.server.naming.NamingDirectory'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
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
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function handle($protocol, $data) {
      $offset= 0;
      $name= $protocol->readString($data, $offset);

      $directory= NamingDirectory::getInstance();
      $this->setValue($directory->lookup($name));
    }
  }
?>
