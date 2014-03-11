<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.log.Appender');

  /**
   * Appender which appends data to a file
   *
   * Supported parameters:
   * <ul><li>string filename - the file name to log to; may contain strftime() token which
   *     will be automatically replaced
   * </li><li>boolean syncDate -  whether to keep filename constant after first resolution;
   *     this is a BC feature
   * </li><li>int perms; file permissions
   * </li></ul>
   *
   * @see   xp://util.log.Appender
   * @test  xp://net.xp_framework.unittest.logging.FileAppenderTest
   */  
  class FileAppender extends Appender {
    public 
      $filename = '',
      $perms    = NULL,
      $syncDate = TRUE;
    
    /**
     * Constructor
     *
     * @param   string filename default 'php://stderr' filename to log to
     */
    public function __construct($filename= 'php://stderr') {
      $this->filename= $filename;
    }

    /**
     * Retrieve current log file name
     *
     * @return string
     */
    public function filename() {
      if ($this->syncDate) {
        return strftime($this->filename);
      }

      if (FALSE !== $this->filename) {
        $this->filename= strftime($this->filename);
      }

      return $this->filename;
    }
    
    /**
     * Append data
     *
     * @param   util.log.LoggingEvent event
     */ 
    public function append(LoggingEvent $event) {
      $line= $this->layout->format($event);
      $fn= $this->filename();

      file_put_contents($fn, $line, FILE_APPEND);
      chmod($fn, octdec($this->perms));
    }
  }
?>
