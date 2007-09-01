<?php
/* This class is part of the XP framework
 *
 * $Id: StorageEntry.class.php 9452 2007-02-13 11:35:27Z kiesel $ 
 */

  namespace peer::ftp::server::storage;

  /**
   * This interface describes objects that implement a single storage 
   * entry for FTP servers.
   *
   * @purpose  Storage
   */
  interface StorageEntry {

    /**
     * Deletes an entry
     *
     * @return  bool TRUE to indicate success
     */
    public function delete();

    /**
     * Renames an entry
     *
     * @param   string target
     * @return  bool TRUE to indicate success
     */
    public function rename($target);

    /**
     * Returns the filename including the path (relative to storage root)
     *
     * @return string
     */
    public function getFilename();

    /**
     * Retrieves the (short) name of a storage entry
     *
     * @return  string
     */  
    public function getName();
    
    /**
     * Retrieves the owner's username
     *
     * @return  string
     */  
    public function getOwner();

    /**
     * Retrieves the owner's group name
     *
     * @return  string
     */  
    public function getGroup();
    
    /**
     * Retrieves the size of this storage entry
     *
     * @return  int bytes
     */  
    public function getSize();

    /**
     * Retrieves the modified timestamp of this storage entry
     *
     * @return  int unix timestamp
     */  
    public function getModifiedStamp();
    
    /**
     * Retrieves the permissions of this storage entry expressed in a
     * unix-permission style integer
     *
     * @see     http://www.google.com/search?ie=UTF8&q=Unix%20permissions
     * @return  int
     */  
    public function getPermissions();

    /**
     * Sets the permissions of this storage entry expressed in a
     * unix-permission style integer
     *
     * @param   int permissions
     */  
    public function setPermissions($permissions);

    /**
     * Retrieves the number of links
     *
     * @return  int
     */  
    public function numLinks();
    
  }
?>
