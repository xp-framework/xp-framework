<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.log.Appender');

  /**
   * StreamAppender which appends data to a stream
   *
   * @see      xp://util.log.Appender
   * @test     xp://net.xp_framework.unittest.logging.StreamAppenderTest
   * @purpose  Appender
   */  
  class StreamAppender extends Appender {
    public $stream= NULL;
    
    /**
     * Constructor
     *
     * @param   io.streams.OutputStream stream
     */
    public function __construct(OutputStream $stream) {
      $this->stream= $stream;
    }
    
    /**
     * Append data
     *
     * @param   util.log.LoggingEvent event
     */ 
    public function append(LoggingEvent $event) {
      $this->stream->write($this->layout->format($event));
    }
  }
?>
