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
    var 
      $filename = '',
      $perms    = 600;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string filename default 'php://stderr' filename to log to
     */
    function __construct($filename= 'php://stderr') {
      $this->filename= $filename;
    }
    
    /**
     * Appends log data to the file
     *
     * @access  public
     * @param   mixed* args variables
     */
    function append() {
      static $init= TRUE;
      $fd= fopen($this->filename, 'a');

      if ($init) {
        chmod($this->filename, octdec($this->perms));
        $init= FALSE;
      }
      
      foreach (func_get_args() as $arg) {
        fputs($fd, $this->varSource($arg).' ');
      }
      fputs($fd, "\n");
      fclose($fd);
    }
  }
?>
