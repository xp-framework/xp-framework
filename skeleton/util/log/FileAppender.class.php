<?php
  uses('util.log.LogAppender');
  
  class FileAppender extends LogAppender {
    var 
      $filename;
    
    /**
     * Constructor
     *
     * @param  string filename default 'php://stderr' Dateiname
     */
    function __construct($filename= 'php://stderr') {
      $this->filename= $filename;
      parent::__construct();
    }
    
    /**
     * Fügt die Daten an eine Textdatei an
     *
     * @access public
     * @param  mixed args Variablen
     */
    function append() {
      $fd= fopen($this->filename, 'a');
      foreach (func_get_args() as $arg) {
       fputs($fd, $this->varSource($arg).' ');
      }
      fputs($fd, "\n");
      fclose($fd);
    }
  }
?>
