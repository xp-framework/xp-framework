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
  
    function getProperties($uri) {}
    function setProperty($uri, $name) {}
    function getProperty($uri, $name) {}
  
  }
?>
