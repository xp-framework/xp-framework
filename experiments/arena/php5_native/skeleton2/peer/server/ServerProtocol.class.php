<?php
/* This class is part of the XP framework
 *
 * $Id: ServerProtocol.class.php 8247 2006-10-24 15:29:13Z friebe $ 
 */

  /**
   * Server Protocol
   *
   * @see      xp://peer.server.Server#setProtocol
   * @purpose  Interface
   */
  interface ServerProtocol {
  
    /**
     * Initialize Protocol
     *
     * @access  public
     * @return  bool
     */
    public function initialize();

    /**
     * Handle client connect
     *
     * @access  public
     * @param   &peer.Socket socket
     */
    public function handleConnect(&$socket);

    /**
     * Handle client disconnect
     *
     * @access  public
     * @param   &peer.Socket socket
     */
    public function handleDisconnect(&$socket);
  
    /**
     * Handle client data
     *
     * @access  public
     * @param   &peer.Socket socket
     * @return  mixed
     */
    public function handleData(&$socket);

    /**
     * Handle I/O error
     *
     * @access  public
     * @param   &peer.Socket socket
     * @param   &lang.Exception e
     */
    public function handleError(&$socket, &$e);
  
  }
?>
