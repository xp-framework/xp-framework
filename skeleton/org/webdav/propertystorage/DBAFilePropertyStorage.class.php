<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.dba.DBAFile');

  /**
   * WebDav property storage with DBA files
   *
   * @see      xp://org.webdav.propertystorage.PropertyStorageProvider
   * @purpose  Property storage
   */
  class DBAFilePropertyStorage extends DBAFile {
    var
      $properties= array();
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string filename
     * @param   string handler one of DBH_* handler constants
     * @see     php://dba#dba.requirements Handler decriptions
     */
    function __construct($filename, $handler= DBH_GDBM) {
      parent::__construct($filename, $handler);
      
      // Create the storage file if it doesn't exist
      if (!file_exists($filename)) {
        $this->open(DBO_CREATE);
        $this->close();
      }
    }

    /**
     * Set all properties for specific uri
     *
     * @access  public
     * @param   string uri The URI
     * @param   org.webdav.WebdavProperty[] properties
     */
    function setProperties($uri, $properties) {
      $uri= 'PROP:'.$uri;
      $this->open(DBO_WRITE);
      if ($properties === NULL) {
        if ($this->lookup($uri)) $this->delete($uri);
      } else {
        $this->store($uri, serialize($properties));
      }
      $this->close();
    }
  
    /**
     * Read all properties for specific uri
     *
     * @access  public
     * @param   string uri The URI
     * @return  org.webdav.WebdavProperty[]
     */
    function getProperties($uri) {
      if (!isset($this->properties[$uri])) {
        $uri= 'PROP:'.$uri;
        $this->open(DBO_READ);
        $this->properties[$uri]= $this->lookup($uri) ? unserialize($this->fetch($uri)) : NULL;
        $this->close();
      }
      return $this->properties[$uri];
    }
  
    /**
     * Sets a property for a specific URI
     *
     * @access  public
     * @param   string uri The URI
     * @param   &org.webdav.WebdavProperty property The WebDav property (use NULL to remove property)
     */
    function setProperty($uri, &$property) {
      $name= $property->getName();
      $prefix= $property->getNameSpacePrefix();
      $properties= $this->getProperties($uri);
      if ($property !== NULL) {
        $properties[$prefix.$name]= $property;
      } else if (isset($properties[$name])) {
        unset($properties[$name]);
      }
      $this->setProperties($uri, $properties);
    }
    
    /**
     * Retrieve property for specific URI
     *
     * @access  public
     * @param   string uri  The URI
     * @param   string name The property's name
     * @return  &org.webdav.WebdavProperty
     */
    function &getProperty($uri, $name) {
      $properties= $this->getProperties($uri);
      return isset($properties[$name]) ? $properties[$name] : NULL;
    }
    
    /**
     * Check if property is available
     *
     * @access  public
     * @param   string uri  The URI
     * @param   string name The property's name
     * @return  bool
     */
    function hasProperty($uri, $name) {
      $properties= $this->getProperties($uri);
      return isset($properties[$name]);
    }
    
    /**
     * Sets a Lock for a specific URI
     *
     * @access public
     * @param  string uri The URI
     * @param  org.webdav.WebdavLock The WebDav lock
     */
    function setLock($uri, &$lock) {
      $uri= 'LOCK:'.$uri;
      $this->open(DBO_WRITE);
      $ret= $this->store($uri, serialize($lock));
      $this->close();
      return $ret;
    }
    
    /**
     * Retrieve lock for specific URI
     *
     * @access  public
     * @param   string uri  The URI
     * @return  &org.webdav.WebdavLock
     */
    function &getLock($uri) {
      $uri= 'LOCK:'.$uri;
      $this->open(DBO_READ);
      $lock= $this->lookup($uri) ? unserialize($this->fetch($uri)) : NULL;
      $this->close();
      return $lock;
    }
    
    /**
     * Deletes a Lock for a specific URI
     *
     * @access public
     * @param  string uri The URI
     */
    function removeLock($uri) {
      $uri= 'LOCK:'.$uri;
      $this->open(DBO_WRITE);
      if ($this->lookup($uri)) $ret= $this->delete($uri);
      $this->close();
      return $ret;
    }
  
  } implements(__FILE__, 'org.webdav.propertystorage.PropertyStorageProvider');
?>
