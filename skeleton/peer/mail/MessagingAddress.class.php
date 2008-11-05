<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Interface for messaging addresses
   *
   * @see      xp://peer.mail.InternetAddress
   */
  interface MessagingAddress {
    
    /**
     * Retrieve address
     *
     * @return  string
     */
    public function getAddress();
  }
?>
