<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.ftp.server.storage.StorageEntry');

  define('SE_READ',   'r');
  define('SE_WRITE',  'w');

  /**
   * This interface describes objects that implement a single storage 
   * element for FTP servers.
   *
   * @see      xp://peer.ftp.server.storage.StorageEntry
   * @purpose  Storage
   */
  class StorageElement extends StorageEntry {
  
    /**
     * Open this element with a specified mode
     *
     * @access  public
     * @param   string mode of of the SE_* constants
     */
    function open($mode) { }
    
    /**
     * Read a chunk of data from this element
     *
     * @access  public
     * @return  string
     */
    function read() { }
    
    /**
     * Write a chunk of data to this element
     *
     * @access  public
     * @param   string buf
     */
    function write($buf) { }
    
    /**
     * Close this element
     *
     * @access  public
     */
    function close() { }
    
  }
?>
