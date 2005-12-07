<?php
/* This file is part of the XP framework's EASC API
 *
 * $Id$
 */

  /**
   * Message sender remote interface
   *
   * @purpose  Demo class  
   */
  class MessageSender extends Interface {
  
    /**
     * Adds the two given arguments
     *
     * @access  public
     * @param   string queueName
     * @param   string text
     */
    function sendTextMessage($queueName, $text) { }
  
  }
?>
