<?php
/* This class is part of the XP framework
 *
 * $Id: EascMessageFactory.class.php 9302 2007-01-16 17:01:53Z kiesel $ 
 */

  namespace remote::server::message;

  uses(
    'remote.protocol.XpProtocolConstants',
    'remote.server.message.EascInitMessage',
    'remote.server.message.EascLookupMessage',
    'remote.server.message.EascValueMessage',
    'remote.server.message.EascExceptionMessage',
    'remote.server.message.EascCallMessage'
  );

  /**
   * Factory class for EASC message classes
   *
   * @purpose  Create EASC message
   */
  class EascMessageFactory extends lang::Object {
  
    /**
     * Factory method
     *
     * @param   int type
     * @return  lang.XPClass
     */
    public static function forType($type) {
      $handlers= array(
        REMOTE_MSG_INIT       => 'Init',
        REMOTE_MSG_LOOKUP     => 'Lookup',
        REMOTE_MSG_CALL       => 'Call',
        REMOTE_MSG_VALUE      => 'Value',
        REMOTE_MSG_EXCEPTION  => 'Exception'
      );
      
      return lang::XPClass::forName(sprintf('remote.server.message.Easc%sMessage', $handlers[$type]))->newInstance();
    }
  }
?>
