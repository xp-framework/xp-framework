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
    protected
      $lastIdentifier= FALSE;
    
    public
      $identifier,
      $facility;

    /**
     * Constructor
     *
     * @param   string identifier default NULL if omitted, defaults to script's filename
     * @param   int facility default LOG_USER
     * @see     php://openlog for valid facility values
     */
    public function __construct($identifier= NULL, $facility= LOG_USER) {
      $this->identifier= $identifier;
      $this->facility= $facility;
    }
    
    /**
     * Append data
     *
     * @param   util.log.LoggingEvent event
     */ 
    public function append(LoggingEvent $event) {
      if ($this->identifier != $this->lastIdentifier) {
        closelog();
        openlog(
          $this->identifier ? $this->identifier : basename($_SERVER['PHP_SELF']), 
          LOG_ODELAY | LOG_PID, 
          $this->facility
        );
        $this->lastIdentifier= $this->identifier;
      }
    
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
      $this->lastIdentifier= FALSE;
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
