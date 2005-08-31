<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Path condition for interceptors
   *
   * @purpose  Path condition
   */
  class PathCondition extends Object {
  
    /**
     * Constructor
     *
     * @access public
     * @param string path The path to match
     */
    function __construct($path) {
      $this->path= $path;
    }
  
    /**
     * Checks if the path matchs
     *
     * @access public
     * @param peer.ftp.server.FtpSession session
     * @param peer.ftp.server.storage.StorageEntry entry
     * @return bool
     */
    function check(&$session, &$entry) {
    
      // Check if the entry's path start with the path to check
      return substr($entry->getFilename(), 0, strlen($this->path)) == $this->path;
    }
  
  } implements(__FILE__, 'peer.ftp.server.interceptor.InterceptorCondition');
?>
