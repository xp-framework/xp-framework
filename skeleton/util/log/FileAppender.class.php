<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.log.Appender');

  /**
   * Appender which appends data to a file
   *
   * @see      xp://util.log.Appender
   * @purpose  Appender
   */  
  class FileAppender extends Appender {
    public 
      $filename = '',
      $perms    = NULL;
    
    /**
     * Constructor
     *
     * @param   string filename default 'php://stderr' filename to log to
     */
    public function __construct($filename= 'php://stderr') {
      $this->filename= $filename;
    }
    
    /**
     * Append data
     *
     * @param   util.log.LoggingEvent event
     */ 
    public function append(LoggingEvent $event) {
      $line= $this->layout->format($event);
      $fd= fopen($this->filename, 'a');

      if ($this->perms) {
        chmod($this->filename, octdec($this->perms));
        $this->perms= NULL;
      }
      
      fputs($fd, $line);
      fclose($fd);
    }
  }
?>
