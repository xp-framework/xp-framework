<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.ftp.server.storage.StorageEntry');

  /**
   * This interface describes objects that implement a single storage 
   * element for FTP servers.
   *
   * @see      xp://peer.ftp.server.storage.StorageEntry
   * @purpose  Storage
   */
  class StorageCollection extends StorageEntry {

    /**
     * Retrieves a list of elements
     *
     * @access  public
     * @return  &peer.ftp.server.storage.StorageEntry[]
     */
    function &elements() { }
  
  }
?>
