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
  interface StorageElement extends StorageEntry {
  
    /**
     * Open this element with a specified mode
     *
     * @param   string mode of of the SE_* constants
     */
    public function open($mode);
    
    /**
     * Read a chunk of data from this element
     *
     * @return  string
     */
    public function read();
    
    /**
     * Write a chunk of data to this element
     *
     * @param   string buf
     */
    public function write($buf);
    
    /**
     * Close this element
     *
     */
    public function close();
    
  }
?>
