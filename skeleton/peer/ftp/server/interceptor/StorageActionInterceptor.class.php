<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Interface class for FTP storage interceptors
   *
   * @see      peer.ftp.server.FtpConnectionListener
   * @purpose  Interceptor interface
   */
  class StorageActionInterceptor extends Interface {
  
    /**
     * Invoked when an entry is created
     * 
     * @access public
     * @param &peer.ftp.server.FtpSession
     * @param &peer.ftp.server.storage.StorageEntry
     * @return bool
     */
    function onCreate(&$session, &$entry) { }
  
    /**
     * Invoked when an entry is deleted
     * 
     * @access public
     * @param &peer.ftp.server.FtpSession
     * @param &peer.ftp.server.storage.StorageEntry
     * @return bool
     */
    function onDelete(&$session, &$entry) { }
  
    /**
     * Invoked when an entry is read
     * 
     * @access public
     * @param &peer.ftp.server.FtpSession
     * @param &peer.ftp.server.storage.StorageEntry
     * @return bool
     */
    function onRead(&$session, &$entry) { }
  
    /**
     * Invoked when an entry is renamed
     * 
     * @access public
     * @param &peer.ftp.server.FtpSession
     * @param &peer.ftp.server.storage.StorageEntry
     * @return bool
     */
    function onRename(&$session, &$entry) { }
  
    /**
     * Invoked when permissions are changed for an entry
     * 
     * @access public
     * @param &peer.ftp.server.FtpSession
     * @param &peer.ftp.server.storage.StorageEntry
     * @return bool
     */
    function onChangePermissions(&$session, &$entry) { }
  }
?>
