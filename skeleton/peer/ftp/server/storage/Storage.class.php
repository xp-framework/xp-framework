<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  define('ST_ELEMENT',     0x0000);
  define('ST_COLLECTION',  0x0001);

  /**
   * This interface describes objects that implement a storage for FTP
   * servers.
   *
   * @purpose  Storage
   */
  class Storage extends Interface {

    /**
     * Sets base
     *
     * @access  public
     * @param   string uri
     * @return  string new base
     */
    function setBase($uri) { }
    
    /**
     * Retrieves base
     *
     * @access  public
     * @return  string
     */
    function getBase() { }

    /**
     * Looks up a element. Returns a StorageCollection, a StorageElement 
     * or NULL in case it nothing is found.
     *
     * @access  public
     * @param   string uri
     * @return  &peer.ftp.server.storage.StorageEntry
     */
    function &lookup($uri) { }

    /**
     * Creates a new StorageEntry and return it
     *
     * @access  public
     * @param   string uri
     * @param   int type one of the ST_* constants
     * @return  &peer.ftp.server.storage.StorageEntry
     */
    function &create($uri, $type) { }
  
  }
?>
