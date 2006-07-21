<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * This interface describes objects that implement a single storage 
   * entry for FTP servers.
   *
   * @purpose  Storage
   */
  interface StorageEntry {

    /**
     * Constructor
     *
     * @access  public
     * @return  string uri
     */
    public function __construct($uri);

    /**
     * Deletes an entry
     *
     * @access  public
     * @return  bool TRUE to indicate success
     */
    public function delete();

    /**
     * Renames an entry
     *
     * @access  public
     * @param   string target
     * @return  bool TRUE to indicate success
     */
    public function rename($target);

    /**
     * Returns the filename including the path (relative to storage root)
     *
     * @access public
     * @return string
     */
    public function getFilename();

    /**
     * Retrieves the (short) name of a storage entry
     *
     * @access  public
     * @return  string
     */  
    public function getName();
    
    /**
     * Retrieves the owner's username
     *
     * @access  public
     * @return  string
     */  
    public function getOwner();

    /**
     * Retrieves the owner's group name
     *
     * @access  public
     * @return  string
     */  
    public function getGroup();
    
    /**
     * Retrieves the size of this storage entry
     *
     * @access  public
     * @return  int bytes
     */  
    public function getSize();

    /**
     * Retrieves the modified timestamp of this storage entry
     *
     * @access  public
     * @return  int unix timestamp
     */  
    public function getModifiedStamp();
    
    /**
     * Retrieves the permissions of this storage entry expressed in a
     * unix-permission style integer
     *
     * @see     http://www.google.com/search?ie=UTF8&q=Unix%20permissions
     * @access  public
     * @return  int
     */  
    public function getPermissions();

    /**
     * Sets the permissions of this storage entry expressed in a
     * unix-permission style integer
     *
     * @access  public
     * @param   int permissions
     */  
    public function setPermissions($permissions);

    /**
     * Retrieves the number of links
     *
     * @access  public
     * @return  int
     */  
    public function numLinks();
    
  }
?>
