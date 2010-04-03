<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.log.Appender');

  /**
   * Appender which appends data to console. The data goes to STDERR.
   *
   * Note: STDERR will not be defined in a web server's environment,
   * so using this class will have no effect - have a look at the
   * SyslogAppender or FileAppender classes instead.
   *
   * @see      xp://util.log.Appender
   * @purpose  Appender
   */  
  class ConsoleAppender extends Appender {
    
    /**
     * Append data
     *
     * @param   util.log.LoggingEvent event
     */ 
    public function append(LoggingEvent $event) {
      fwrite(STDERR, $this->layout->format($event));
    }
  }
?>
