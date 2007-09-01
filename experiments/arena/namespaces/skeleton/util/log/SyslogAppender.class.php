<?php
/* This class is part of the XP framework
 *
 * $Id: SyslogAppender.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace util::log;

  ::uses('util.log.LogAppender');

  /**
   * LogAppender which appends data to syslog
   *
   * @see      xp://util.log.LogAppender
   * @see      php://syslog
   * @purpose  Appender
   */  
  class SyslogAppender extends LogAppender {

    /**
     * Destructor.
     *
     */
    public function __destruct() {
      $this->finalize();
    }
        
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
     * Appends log data to the syslog
     *
     * @param   mixed args variables
     */
    public function append() {
      $buf= '';
      foreach (func_get_args() as $arg) {
        $buf.= $this->varSource($arg).' ';
      }
      syslog($buf);
    }
    
    /**
     * Finalize this appender - is called when the logger shuts down
     * at the end of the request.
     *
     */
    public function finalize() {
      closelog();
    }
  }
?>
