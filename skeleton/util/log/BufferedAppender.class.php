<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.log.LogAppender');

  /**
   * Appender which appends all data to an appender
   *
   * @see   xp://util.log.LogAppender
   */  
  class BufferedAppender extends LogAppender {
    var 
      $buffer = '';

    /**
     * Appends log data to the buffer
     *
     * @access public
     * @param  mixed args variables
     */
    function append() {
      foreach (func_get_args() as $arg) {
        $this->buffer.= $this->varSource($arg).' ';
      }
      $this->buffer.= "\n";
    }
    
    /**
     * Get Buffer
     *
     * @access  public
     * @return  string
     */
    function getBuffer() {
      return $this->buffer;
    }
  }
?>
