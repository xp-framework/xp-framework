<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.log.LogAppender');

  /**
   * LogAppender which appends data to a file
   *
   * @see      xp://util.log.LogAppender
   * @purpose  Appender
   */  
  class FileAppender extends LogAppender {
    public
      $filename = '';
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string filename default 'php://stderr' filename to log to
     */
    public function __construct($filename= 'php://stderr') {
      $this->filename= $filename;
      parent::__construct();
    }
    
    /**
     * Appends log data to the file
     *
     * @access  public
     * @param   mixed* args variables
     */
    public function append() {
      $fd= fopen($this->filename, 'a');
      foreach (func_get_args() as $arg) {
        fputs($fd, self::varSource($arg).' ');
      }
      fputs($fd, "\n");
      fclose($fd);
    }
  }
?>
