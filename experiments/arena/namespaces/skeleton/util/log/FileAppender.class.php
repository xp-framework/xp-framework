<?php
/* This class is part of the XP framework
 *
 * $Id: FileAppender.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace util::log;

  ::uses('util.log.LogAppender');

  /**
   * LogAppender which appends data to a file
   *
   * @see      xp://util.log.LogAppender
   * @purpose  Appender
   */  
  class FileAppender extends LogAppender {
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
     * Appends log data to the file
     *
     * @param   mixed* args variables
     */
    public function append() {
      $fd= fopen($this->filename, 'a');

      if ($this->perms) {
        chmod($this->filename, octdec($this->perms));
        $this->perms= NULL;
      }
      
      with ($args= func_get_args()); {
        foreach ($args as $idx => $arg) {
          fputs($fd, $this->varSource($arg). ($idx < sizeof($args)-1 ? ' ' : ''));
        }
      }

      fputs($fd, "\n");
      fclose($fd);
    }
  }
?>
