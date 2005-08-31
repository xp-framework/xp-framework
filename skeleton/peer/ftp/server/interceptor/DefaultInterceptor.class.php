<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Default implementation of a StorageActionInterceptor which just
   * returns TRUE in any case.
   *
   * @purpose  Interceptor
   */
  class DefaultInterceptor extends Object {

    /**
     * Invoked when an entry is created
     * 
     * @access public
     * @param &peer.ftp.server.FtpSession
     * @param &peer.ftp.server.storage.StorageEntry
     * @return bool
     */
    function onCreate(&$session, &$entry) {
      return TRUE;
    }
  
    /**
     * Invoked when an entry is deleted
     * 
     * @access public
     * @param &peer.ftp.server.FtpSession
     * @param &peer.ftp.server.storage.StorageEntry
     * @return bool
     */
    function onDelete(&$session, &$entry) {
      return TRUE;
    }
  
    /**
     * Invoked when an entry is read
     * 
     * @access public
     * @param &peer.ftp.server.FtpSession
     * @param &peer.ftp.server.storage.StorageEntry
     * @return bool
     */
    function onRead(&$session, &$entry) {
      return TRUE;
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
      return TRUE;
    }
  
    /**
     * Invoked when permissions are changed for an entry
     * 
     * @access public
     * @param &peer.ftp.server.FtpSession
     * @param &peer.ftp.server.storage.StorageEntry
     * @return bool
     */
    function onChangePermissions(&$session, &$entry) {
      return TRUE;
    }
  } implements(__FILE__, 'peer.ftp.server.interceptor.StorageActionInterceptor');
?>
