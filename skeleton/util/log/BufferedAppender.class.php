<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.log.Appender');

  /**
   * Appender which appends all data to a buffer
   *
   * @see      xp://util.log.Appender
   * @purpose  Appender
   */  
  class BufferedAppender extends Appender {
    public $buffer= '';

    /**
     * Append data
     *
     * @param   util.log.LoggingEvent event
     */ 
    public function append(LoggingEvent $event) {
      $this->buffer.= $this->layout->format($event);
    }
    
    /**
     * Get buffer's contents
     *
     * @return  string
     */
    public function getBuffer() {
      return $this->buffer;
    }
    
    /**
     * Clears the buffers content.
     *
     */
    public function clear() {
      $this->buffer= '';
    }    
  }
?>
