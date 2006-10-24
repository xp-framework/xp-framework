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
     * Initialize Protocol
     *
     * @access  public
     * @return  bool
     */
    function initialize() { }

    /**
     * Handle client connect
     *
     * @access  public
     * @param   &peer.Socket socket
     */
    function handleConnect(&$socket) { }

    /**
     * Handle client disconnect
     *
     * @access  public
     * @param   &peer.Socket socket
     */
    function handleDisconnect(&$socket) { }
  
    /**
     * Handle client data
     *
     * @access  public
     * @param   &peer.Socket socket
     * @return  mixed
     */
    function handleData(&$socket) { }

    /**
     * Handle I/O error
     *
     * @access  public
     * @param   &peer.Socket socket
     * @param   &lang.Exception e
     */
    function handleError(&$socket, &$e) { }
  
  }
?>
