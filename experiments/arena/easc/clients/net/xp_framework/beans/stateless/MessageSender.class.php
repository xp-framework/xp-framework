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
     * Sends a text message
     *
     * @access  public
     * @param   string queueName
     * @param   string text
     */
    function sendTextMessage($queueName, $text) { }

    /**
     * Sends a map message
     *
     * @access  public
     * @param   string queueName
     * @param   array<string, mixed> map
     */
    function sendMapMessage($queueName, $map) { }
  
  }
?>
