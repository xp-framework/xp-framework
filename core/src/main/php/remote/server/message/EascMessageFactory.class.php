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
   * Factory class for EASC message classes
   *
   * @purpose  Create EASC message
   */
  abstract class EascMessageFactory extends Object {
    protected static 
      $handlers= array();
    
    static function __static() {
      self::$handlers[REMOTE_MSG_INIT]= XPClass::forName('remote.server.message.EascInitMessage');
      self::$handlers[REMOTE_MSG_LOOKUP]= XPClass::forName('remote.server.message.EascLookupMessage');
      self::$handlers[REMOTE_MSG_CALL]= XPClass::forName('remote.server.message.EascCallMessage');
      self::$handlers[REMOTE_MSG_VALUE]= XPClass::forName('remote.server.message.EascValueMessage');
      self::$handlers[REMOTE_MSG_EXCEPTION]= XPClass::forName('remote.server.message.EascExceptionMessage');
    }
  
    /**
     * Set handler for a given type to 
     *
     * @param   int type
     * @param   lang.XPClass class
     */
    public static function setHandler($type, XPClass $class) {
      self::$handlers[$type]= $class;
    }
  
    /**
     * Factory method
     *
     * @param   int type
     * @return  remote.server.message.EascMessage
     * @throws  lang.IllegalArgumentException if no message exists for this type.
     */
    public static function forType($type) {
      if (!isset(self::$handlers[$type])) {
        throw new IllegalArgumentException('Unknown message type '.$type);
      }
      return self::$handlers[$type]->newInstance();
    }
  }
?>
