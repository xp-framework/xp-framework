<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  // Magic XP protocol number
  define('DEFAULT_PROTOCOL_MAGIC_NUMBER', 0x3c872747);

  // Request messages
  define('REMOTE_MSG_INIT',      0x0000);
  define('REMOTE_MSG_LOOKUP',    0x0001);
  define('REMOTE_MSG_CALL',      0x0002);
  define('REMOTE_MSG_FINALIZE',  0x0003);
  define('REMOTE_MSG_TRAN_OP',   0x0004);
  
  // Response messages
  define('REMOTE_MSG_VALUE',     0x0005);
  define('REMOTE_MSG_EXCEPTION', 0x0006);
  define('REMOTE_MSG_ERROR',     0x0007);
  
  // Transaction message types
  define('REMOTE_TRAN_BEGIN',    0x0001);
  define('REMOTE_TRAN_STATE',    0x0002);
  define('REMOTE_TRAN_COMMIT',   0x0003);
  define('REMOTE_TRAN_ROLLBACK', 0x0004);

  /**
   * Utility class for XpProtocol constants
   *
   * @see      xp://remote.protocol.XpProtocolHandler
   * @purpose  Constant container class
   */
  class XpProtocolConstants extends Object {
  
    /**
     * Fetch name of message constant by its value
     *
     * @param   int id
     * @return  string
     */
    public static function nameOfMessage($id) {
      $names= array(
        REMOTE_MSG_INIT       => 'REMOTE_MSG_INIT',
        REMOTE_MSG_LOOKUP     => 'REMOTE_MSG_LOOKUP',
        REMOTE_MSG_CALL       => 'REMOTE_MSG_CALL',
        REMOTE_MSG_FINALIZE   => 'REMOTE_MSG_FINALIZE',
        REMOTE_MSG_TRAN_OP    => 'REMOTE_MSG_TRAN_OP',
        REMOTE_MSG_VALUE      => 'REMOTE_MSG_VALUE',
        REMOTE_MSG_EXCEPTION  => 'REMOTE_MSG_EXCEPTION',
        REMOTE_MSG_ERROR      => 'REMOTE_MSG_ERROR'
      );
      
      return (isset($names[$id])
        ? $names[$id]
        : '<unknown>'
      );
    }
    
    /**
     * Fetch name of transaction constant by its value
     *
     * @param   int id
     * @return  string
     */
    public static function nameOfTransaction($id) {
      $names= array(
        REMOTE_TRAN_BEGIN   => 'REMOTE_TRAN_BEGIN',
        REMOTE_TRAN_STATE   => 'REMOTE_TRAN_STATE',
        REMOTE_TRAN_COMMIT  => 'REMOTE_TRAN_COMMIT',
        REMOTE_TRAN_ROLLBACK  => 'REMOTE_TRAN_ROLLBACK'
      );
      
      return (isset($names[$id])
        ? $names[$id]
        : '<unknown>'
      );
    }
  }
?>
