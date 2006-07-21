<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.log.LogAppender');

  /**
   * Appender which appends all data to a buffer
   *
   * @see      xp://util.log.LogAppender
   * @purpose  Appender
   */  
  class BufferedAppender extends LogAppender {
    public 
      $buffer = '';

    /**
     * Appends log data to the buffer
     *
     * @access public
     * @param  mixed* args variables
     */
    public function append() {
      foreach (func_get_args() as $arg) {
        $this->buffer.= $this->varSource($arg).' ';
      }
      $this->buffer.= "\n";
    }
    
    /**
     * Get buffer's contents
     *
     * @access  public
     * @return  string
     */
    public function getBuffer() {
      return $this->buffer;
    }
    
    /**
     * Clears the buffers content.
     *
     * @access  public
     */
    public function clear() {
      $this->buffer= '';
    }    
  }
?>
