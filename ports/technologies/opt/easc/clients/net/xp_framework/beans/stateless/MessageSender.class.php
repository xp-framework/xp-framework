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
  interface MessageSender {
  
    /**
     * Sends a text message
     *
     * @param   string queueName
     * @param   string text
     */
    public function sendTextMessage($queueName, $text);

    /**
     * Sends a map message
     *
     * @param   string queueName
     * @param   array<string, mixed> map
     */
    public function sendMapMessage($queueName, $map);
  
  }
?>
