<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Server Protocol
   *
   * @see      xp://peer.server.Server#setProtocol
   * @purpose  Interface
   */
  class ServerProtocol extends Interface {
  
    /**
     * Handle client connect
     *
     * @access  public
     * @param   &peer.Socket
     */
    function handleConnect(&$socket) { }

    /**
     * Handle client disconnect
     *
     * @access  public
     * @param   &peer.Socket
     */
    function handleDisconnect(&$socket) { }
  
    /**
     * Handle client data
     *
     * @access  public
     * @param   &peer.Socket
     * @return  mixed
     */
    function handleData(&$socket) { }

    /**
     * Handle I/O error
     *
     * @access  public
     * @param   &peer.Socket
     * @param   &lang.Exception e
     */
    function handleError(&$socket, &$e) { }
  
  }
?>
