<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses('util.Date');

  /**
   * Utils for webdav versioning
   *
   * @purpose  versioning utils
   */
  class WebdavVersionUtil extends Object {
  
    /**
     * Creates new version object relating on latest version
     *
     * @access  public
     * @param   &org.webdav.version.Webdav*Version
     * @return  &org.webdav.version.Webdav*Version
     */
    function &getNextVersion(&$actVersion, $fileuri) {
    
      // Load same type of version as before
      $obj= &XPClass::forName(XP::typeOf($actVersion));
    
      // Create new version object 
      with ($version= &$obj->newInstance($actVersion->getFilename())); {
        $version->setVersionNumber($actVersion->getVersionNumber()+0.1);
        $version->setHref('../versions/'.$version->getFilename().'_'.$version->getVersionNumber());
        $version->setVersionName($version->getFilename().'_'.$version->getVersionNumber());
        $version->setContentLength(sizeof($fileuri));
        $version->setLastModified(Date::now());
      }
    
      return $version;  
    }
  
  }
?>
