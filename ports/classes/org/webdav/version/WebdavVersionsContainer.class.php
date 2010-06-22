<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Container which collects all versions of file
   *
   * @see      org.webdav.DavImpl#VersionControl
   * @purpose  Container of versions
   */
  class WebdavVersionsContainer extends Object {
    public
      $versions= array();
  
    /**
     * Construct
     *
     * @param   org.webdav.version.Webdav*Version
     */
    public function __construct($version= NULL) {
      if ($version !== NULL) $this->addVersion($version);
    }
    
    /**
     * Add a version to the container
     *
     * @param   org.webdav.version.Webdav*Version
     */
    public function addVersion($version) {
      $this->versions[]= $version;
    }
    
    /**
     * Get all versions
     *
     * @return  array versions
     */
    public function getVersions() {
      return $this->versions;
    }
    
    /**
     * Returns the last added version object
     *
     * @return  org.webdav.version.Webdav*Version
     */
    public function getLatestVersion() {
      return end($this->versions);
    }
  
  }
?>
