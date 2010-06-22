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
  interface PropertyStorageProvider {

    /**
     * Set all properties for specific uri
     *
     * @param   string uri The URI
     * @param   org.webdav.WebdavProperty[] properties
     */
    public function setProperties($uri, $properties);
  
    /**
     * Read all properties for specific uri
     *
     * @param   string uri The URI
     * @return  org.webdav.WebdavProperty[]
     */
    public function getProperties($uri);

    /**
     * Sets a property for a specific URI
     *
     * @param   string uri The URI
     * @param   org.webdav.WebdavProperty property The WebDav property (use NULL to remove property)
     */
    public function setProperty($uri, $property);

    /**
     * Retrieve property for specific URI
     *
     * @param   string uri  The URI
     * @param   string name The property's name
     * @return  org.webdav.WebdavProperty
     */
    public function getProperty($uri, $name);
    
    /**
     * Check if property is available
     *
     * @param   string uri  The URI
     * @param   string name The property's name
     * @return  bool
     */
    public function hasProperty($uri, $name);
    
    /**
     * Sets a lock for a specific URI
     *
     * @param   string uri The URI
     * @param   org.webdav.WebdavLock The WebDav Lock (use NULL to remove property)
     */
    public function setLock($uri, $property);
    
    /**
     * Retrieve lock for specific URI
     *
     * @param   string uri  The URI
     * @return  org.webdav.WebdavLock
     */
    public function getLock($uri);
    
    /**
     * Removes a lock for specific URI
     *
     * @param   string uri  The URI
     * @return  org.webdav.WebdavLock
     */
    public function removeLock($uri);
    
  }
?>
