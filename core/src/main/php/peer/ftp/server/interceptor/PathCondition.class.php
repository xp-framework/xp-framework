<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.ftp.server.interceptor.InterceptorCondition');

  /**
   * Path condition for interceptors
   *
   * @purpose  Path condition
   */
  class PathCondition extends Object implements InterceptorCondition {
    protected
      $path = NULL;

    /**
     * Constructor
     *
     * @param string path The path to match
     */
    public function __construct($path) {
      $this->path= $path;
    }
  
    /**
     * Checks if the path matchs
     *
     * @param peer.ftp.server.FtpSession session
     * @param peer.ftp.server.storage.StorageEntry entry
     * @return bool
     */
    public function check($session, $entry) {
    
      // Check if the entry's path start with the path to check
      return substr($entry->getFilename(), 0, strlen($this->path)) == $this->path;
    }
  
  } 
?>
