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
    public
      $stream = NULL,
      $hash   = NULL;

    /**
     * Constructor
     *
     * @param   io.Steram stream
     */
    public function __construct($stream) {
      $this->stream= $stream;
    }
    
    /**
     * Parse configuration stream
     *
     */
    protected function _parseFile() {
      $this->stream->open(FILE_MODE_READ);
      
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
     * @return  string
     */
    protected function _readKey() {
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
     * @return  string
     */
    protected function _readValue() {
    
      // Read e.g. "V 6"
      $l= $this->stream->readLine();
      
      $l= $this->stream->readLine();
      return trim($l);
    }
    
    /**
     * Fetch value for a given key
     *
     * @param   string key
     * @return  string value
     */
    public function getValue($key) {
      if (!$this->hash) $this->_parseFile();
      
      if (!isset($this->hash[$key])) return NULL;
      return $this->hash[$key];
    }
  }
?>
