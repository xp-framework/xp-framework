<?php
/* This class is part of the XP framework
 *
 * $Id: ConsoleAppender.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace util::log;

  ::uses('util.log.LogAppender');

  /**
   * LogAppender which appends data to console. The data goes to STDERR.
   *
   * Note: STDERR will not be defined in a web server's environment,
   * so using this class will have no effect - have a look at the
   * SyslogAppender or FileAppender classes instead.
   *
   * @see      xp://util.log.LogAppender
   * @purpose  Appender
   */  
  class ConsoleAppender extends LogAppender {
    
    /**
     * Appends log data to STDERR
     *
     * @param  mixed args variables
     */
    public function append() {
      foreach (func_get_args() as $arg) {
        fwrite(STDERR, $this->varSource($arg).' ');
      }
      fwrite(STDERR, "\n");
    }
  }
?>
