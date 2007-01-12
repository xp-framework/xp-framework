<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'remote.protocol.XpProtocolConstants',
    'remote.server.message.EascInitMessage',
    'remote.server.message.EascLookupMessage',
    'remote.server.message.EascValueMessage',
    'remote.server.message.EascExceptionMessage',
    'remote.server.message.EascCallMessage'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class EascMessageFactory extends Object {
  
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function forType($type) {
      static 
        $handlers=      NULL;
      
      if (NULL === $handlers) {
        $handlers= array(
          REMOTE_MSG_INIT       => 'Init',
          REMOTE_MSG_LOOKUP     => 'Lookup',
          REMOTE_MSG_CALL       => 'Call',
          REMOTE_MSG_VALUE      => 'Value',
          REMOTE_MSG_EXCEPTION  => 'Exception'
        );
      }
      
      $inst= XPClass::forName(sprintf('remote.server.message.Easc%sMessage', $handlers[$type]))->newInstance();
      $inst->setType($type);
      
      return $inst;
    }
  }
?>
