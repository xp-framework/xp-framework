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
  }
?>
