<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Interface for Interceptor condition
   *
   * @purpose  Interceptor condition
   */
  class InterceptorCondition extends Interface {
  
    /**
     * Checks the condition
     *
     * @param peer.ftp.server.FtpSession session
     * @param peer.ftp.server.storage.StorageEntry entry
     * @return bool
     */
    function check(&$session, &$entry) { }
  }
?>
