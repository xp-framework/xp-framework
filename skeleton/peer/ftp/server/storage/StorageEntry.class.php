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
  class StorageEntry extends Interface {

    /**
     * Constructor
     *
     * @access  public
     * @return  string uri
     */
    function __construct($uri) { }

    /**
     * Deletes an entry
     *
     * @access  public
     * @return  bool TRUE to indicate success
     */
    function delete() { }

    /**
     * Renames an entry
     *
     * @access  public
     * @param   string target
     * @return  bool TRUE to indicate success
     */
    function rename($target) { }

    /**
     * Retrieves the (short) name of a storage entry
     *
     * @access  public
     * @return  string
     */  
    function getName() { }
    
    /**
     * Retrieves the owner's username
     *
     * @access  public
     * @return  string
     */  
    function getOwner() { }

    /**
     * Retrieves the owner's group name
     *
     * @access  public
     * @return  string
     */  
    function getGroup() { }
    
    /**
     * Retrieves the size of this storage entry
     *
     * @access  public
     * @return  int bytes
     */  
    function getSize() { }

    /**
     * Retrieves the modified timestamp of this storage entry
     *
     * @access  public
     * @return  int unix timestamp
     */  
    function getModifiedStamp() { }
    
    /**
     * Retrieves the permissions of this storage entry expressed in a
     * unix-permission style integer
     *
     * @see     http://www.google.com/search?ie=UTF8&q=Unix%20permissions
     * @access  public
     * @return  int
     */  
    function getPermissions() { }
    
    /**
     * Retrieve long string representation of this entry. This 
     * representation is used for the LIST command's output and should
     * look like the one you see when using ls -al:
     *
     * Example:
     * <pre>
     *   -rw-r--r--   1 thekid  thekid       738 Jun 24 14:21 stat.diff
     *   drwxr-xr-x   2 thekid  thekid       512 May 21 11:23 sync
     * </pre>
     *
     * @access  public
     * @return  string
     */
    function longRepresentation() { }
    
  }
?>
