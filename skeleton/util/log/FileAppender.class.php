<?php
  class FileAppender extends Object {
    var 
      $filename;
    
    /**
     * Constructor
     *
     * @param  string filename default 'php://stderr' Dateiname
     */
    function __construct($filename= 'php://stderr') {
      $this->filename= $filename;
      Object::__construct();
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
    
    /**
     * Gibt eine Variable schön lesbar zurück
     *
     * @access public
     * @param  mixed var Variable jeglichen Types
     * @return string Ein Printout der Variable
     */
    function varSource($var) {
      if (is_array($var) || is_object($var)) {
        ob_start();
        var_dump($var);
        $var= ob_get_contents();
        ob_end_clean();
      }
      return $var;
    }
  }
?>
