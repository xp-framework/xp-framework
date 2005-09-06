<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses('peer.ftp.server.interceptor.DefaultInterceptor');

  /**
   * Interceptor
   *
   * @purpose  Interceptor
   */
  class EntrynameInterceptor extends DefaultInterceptor {
  
    var
      $regexp= NULL;
  
    /**
     * Constructor
     *
     * @access public
     * @param string regexp Regular expression to match entry name
     */
    function __construct($regexp) {
      $this->regexp= $regexp;
    }
  
    /**
     * Checks if the entry name is valid (ends with special filename
     * extension (e.g. .gif, .jpg)
     *
     * @access private
     * @param string name The entry name
     * @return bool
     */
    function validFilename($name) {
      return preg_match($this->regexp, $name);
    }
  
    /**
     * Invoked when an entry is created
     * 
     * @access public
     * @param &peer.ftp.server.FtpSession
     * @param &peer.ftp.server.storage.StorageEntry
     * @return bool
     */
    function onCreate(&$session, &$entry) {
      if (is('peer.ftp.server.storage.StorageCollection', $entry)) return;
    
      if (!$this->validFilename($entry->getFilename())) {
        return throw(new IllegalAccessException('Invalid filename'));
      }
    }
  
    /**
     * Invoked when an entry is renamed
     * 
     * @access public
     * @param &peer.ftp.server.FtpSession
     * @param &peer.ftp.server.storage.StorageEntry
     * @return bool
     */
    function onRename(&$session, &$entry) {
      if (is('peer.ftp.server.storage.StorageCollection', $entry)) return;

      if (!$this->validFilename($entry->getFilename())) {
        return throw(new IllegalAccessException('Invalid filename'));
      }
    }
  }
?>
