<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Reader class for subversion configuration files
   *
   * @purpose  Read subversion configuration files
   */
  class SVNConfigurationReader extends Object {
    var
      $stream = NULL,
      $hash   = NULL;

    /**
     * Constructor
     *
     * @access  public
     * @param   &io.Steram stream
     */
    function __construct(&$stream) {
      $this->stream= &$stream;
    }
    
    /**
     * Parse configuration stream
     *
     * @access  protected
     */
    function _parseFile() {
      try(); {
        $this->stream->open(FILE_MODE_READ);
      } if (catch('IOException', $e)) {
        return throw($e);
      }
      
      // Read key-value pairs into hash
      $key= TRUE; $value= NULL;
      while ($key) {
        if ($key= $this->_readKey()) {
          $value= $this->_readValue();
        
          $this->hash[$key]= $value;
        }
      }
      
      $this->stream->close();
    }
    
    /**
     * Read key
     *
     * @access  protected 
     * @return  string
     */
    function _readKey() {
      $l= $this->stream->readLine();

      // END token marks end of file      
      if ('END' == trim($l)) return NULL;
      
      // Ignore the line just read, take the next one
      $l= $this->stream->readLine();
      return trim($l);
    }
    
    /**
     * Read value
     *
     * @access  protected
     * @return  string
     */
    function _readValue() {
    
      // Read e.g. "V 6"
      $l= $this->stream->readLine();
      
      $l= $this->stream->readLine();
      return trim($l);
    }
    
    /**
     * Fetch value for a given key
     *
     * @access  public
     * @param   string key
     * @return  string value
     */
    function getValue($key) {
      if (!$this->hash) $this->_parseFile();
      
      if (!isset($this->hash[$key])) return NULL;
      return $this->hash[$key];
    }
  }
?>
