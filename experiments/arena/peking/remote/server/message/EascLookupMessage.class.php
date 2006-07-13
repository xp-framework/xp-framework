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
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function handle(&$listener, &$event) {
      $data= $event->data[1];
      $offset= 0;
      $name= $this->readString($data, $offset);

      $directory= &NamingDirectory::getInstance();
      $proxy= &$directory->lookup($name);
      
      $this->setValue($proxy);
    }
  }
?>
