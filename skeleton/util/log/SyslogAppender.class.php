<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.log.Appender');

  /**
   * Appender which appends data to syslog
   *
   * @see      xp://util.log.Appender
   * @see      php://syslog
   * @purpose  Appender
   */  
  class SyslogAppender extends Appender {

    /**
     * Constructor
     *
     * @param   string identifier default NULL if omitted, defaults to script's filename
     * @param   int facility default LOG_USER
     * @see     php://openlog for valid facility values
     */
    public function __construct($identifier= NULL, $facility= LOG_USER) {
      openlog(
        $identifier ? $identifier : basename($_SERVER['PHP_SELF']), 
        LOG_ODELAY | LOG_PID, 
        $facility
      );
    }
    
    /**
     * Append data
     *
     * @param   util.log.LoggingEvent event
     */ 
    public function append(LoggingEvent $event) {
      static $map= array(
        LogLevel::INFO    => LOG_INFO,
        LogLevel::WARN    => LOG_WARNING,
        LogLevel::ERROR   => LOG_ERR,
        LogLevel::DEBUG   => LOG_DEBUG,
        LogLevel::NONE    => LOG_NOTICE
      );

      $l= $event->getLevel();
      syslog($map[isset($map[$l]) ? $l : LogLevel::NONE], $this->layout->format($event));
    }
    
    /**
     * Finalize this appender - is called when the logger shuts down
     * at the end of the request.
     *
     */
    public function finalize() {
      closelog();
    }

    /**
     * Destructor.
     *
     */
    public function __destruct() {
      $this->finalize();
    }
  }
?>
