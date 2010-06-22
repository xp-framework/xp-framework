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
     * @param   org.webdav.version.Webdav*Version
     * @param   io.File file
     * @return  org.webdav.version.Webdav*Version
     */
    public function getNextVersion($actVersion, $file) {
      // Load same type of version as before
      $obj= XPClass::forName(XP::typeOf($actVersion));
      
      // Get name of file, without extension    
      $fname= basename($actVersion->getFilename(), '.'.$file->getExtension());
      
      // Get name of directory
      $dir= substr(dirname($actVersion->getHref()), 12);
      
      // Create new version object 
      with ($version= $obj->newInstance($actVersion->getFilename())); {
        $version->setVersionNumber($actVersion->getVersionNumber()+0.1);
        $version->setHref('../versions/'.$dir.'/'.$fname.'['.$version->getVersionNumber().'].'.$file->getExtension());
        $version->setVersionName($fname.'['.$version->getVersionNumber().'].'.$file->getExtension());
        $version->setContentLength($file->size());
        $version->setLastModified(Date::now());
      }
    
      return $version;  
    }
  
  }
?>
