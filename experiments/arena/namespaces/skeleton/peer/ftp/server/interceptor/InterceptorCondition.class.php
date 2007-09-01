<?php
/* This class is part of the XP framework
 *
 * $Id: InterceptorCondition.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace peer::ftp::server::interceptor;

  /**
   * Interface for Interceptor condition
   *
   * @purpose  Interceptor condition
   */
  interface InterceptorCondition {
  
    /**
     * Checks the condition
     *
     * @param peer.ftp.server.FtpSession session
     * @param peer.ftp.server.storage.StorageEntry entry
     * @return bool
     */
    public function check($session, $entry);
  }
?>
