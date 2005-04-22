<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.reflect.InvocationHandler');

  define('REMOTE_MSG_INIT',      0x0000);
  define('REMOTE_MSG_REQUEST',   0x0001);
  define('REMOTE_MSG_REPLY',     0x0002);
  define('REMOTE_MSG_PING',      0x0003);
  define('REMOTE_MSG_CLOSE',     0x0004);

  /**
   * (Insert class' description here)
   *
   * @see      xp://lang.reflect.InvocationHandler
   * @purpose  Interface for all protocol handlers
   */
  class ProtocolHandler extends InvocationHandler {

    /**
     * Initialize
     *
     * @access  package
     * @param   &peer.URL url
     * @param   string interface
     * @param   string opt
     */
    function initializeFor(&$url, $interface, $opt) { }
  
  }
?>
