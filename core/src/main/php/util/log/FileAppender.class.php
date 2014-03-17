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
   * </li><li>bool syncDate - whether to recalculate the log file name for every line written.
   *     Set this to FALSE to calculcate it only once.
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
    public function filename($ref= NULL) {
      $formatted= NULL === $ref ? strftime($this->filename) : strftime($this->filename, $ref);
      if (!$this->syncDate) {
        $this->filename= $formatted;
      }
      return $formatted;
    }
    
    /**
     * Append data
     *
     * @param   util.log.LoggingEvent event
     */ 
    public function append(LoggingEvent $event) {
      $fn= $this->filename();
      file_put_contents($fn, $this->layout->format($event), FILE_APPEND);
      $this->perms && chmod($fn, octdec($this->perms));
    }
  }
?>
