<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Interface for webdav property storage
   *
   * @purpose  Interface
   */
  class PropertyStorageProvider extends Interface {

    /**
     * Set all properties for specific uri
     *
     * @access  public
     * @param   string uri The URI
     * @param   org.webdav.WebdavProperty[] properties
     */
    function setProperties($uri, $properties) { }
  
    /**
     * Read all properties for specific uri
     *
     * @access  public
     * @param   string uri The URI
     * @return  org.webdav.WebdavProperty[]
     */
    function getProperties($uri) { }

    /**
     * Sets a property for a specific URI
     *
     * @access  public
     * @param   string uri The URI
     * @param   &org.webdav.WebdavProperty property The WebDav property (use NULL to remove property)
     */
    function setProperty($uri, &$property) { }

    /**
     * Retrieve property for specific URI
     *
     * @access  public
     * @param   string uri  The URI
     * @param   string name The property's name
     * @return  &org.webdav.WebdavProperty
     */
    function &getProperty($uri, $name) { }
    
    /**
     * Check if property is available
     *
     * @access  public
     * @param   string uri  The URI
     * @param   string name The property's name
     * @return  bool
     */
    function hasProperty($uri, $name) { }
    
    /**
     * Sets a lock for a specific URI
     *
     * @access  public
     * @param   string uri The URI
     * @param   &org.webdav.WebdavLock The WebDav Lock (use NULL to remove property)
     */
    function setLock($uri, &$property) { }
    
    /**
     * Retrieve lock for specific URI
     *
     * @access  public
     * @param   string uri  The URI
     * @return  &org.webdav.WebdavLock
     */
    function &getLock($uri) { }
    
    /**
     * Removes a lock for specific URI
     *
     * @access  public
     * @param   string uri  The URI
     * @return  &org.webdav.WebdavLock
     */
    function &removeLock($uri) { }
    
  }
?>
