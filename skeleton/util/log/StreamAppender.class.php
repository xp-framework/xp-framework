<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.log.LogAppender');

  /**
   * StreamAppender which appends data to a stream
   *
   * @see      xp://util.log.LogAppender
   * @purpose  Appender
   */  
  class StreamAppender extends LogAppender {
    public 
      $stream = NULL;
    
    /**
     * Constructor
     *
     * @param   io.streams.OutputStream stream
     */
    public function __construct($stream) {
      $this->stream= $stream;
    }
    
    /**
     * Appends log data to the file
     *
     * @param   mixed* args variables
     */
    public function append() {
      with ($args= func_get_args()); {
        foreach ($args as $idx => $arg) {
          $this->stream->write($this->varSource($arg). ($idx < sizeof($args)-1 ? ' ' : ''));
        }
      }
      $this->stream->write("\n");
    }
  }
?>
