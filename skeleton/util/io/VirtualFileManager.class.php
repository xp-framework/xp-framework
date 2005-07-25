<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses ('io.Stream');
  
  /**
   * A file manager singleton to provide access to 
   * not really existing files
   *
   * @model   singleton
   * @purpose Provide access to nonexistant files
   */
  class VirtualFileManager extends Object {
    
    /**
     * Constructor
     *
     * @access  public
     */
    function __construct() {
      $this->fileMap= array();
    }
    
    /**
     * Gets the instance of the VirtualFileManager
     *
     * @access  public
     * @return  &io.VirtualFileManager
     */
    function &getInstance() {
      static $instance= NULL;
      
      if (NULL === $instance) $instance= new VirtualFileManager();
      return $instance;
    }
    
    /**
     * Adds a file
     *
     * @access  public
     * @param   string path
     * @param   &string data
     */
    function addFile($path, &$data) {
      $stream= &new Stream();
      $stream->open (STREAM_MODE_READWRITE);
      $stream->write ($data);
      $stream->rewind();
      
      $this->fileMap[$path]= &$data;
    }
    
    /**
     * Retrieves a file
     *
     * @access  public
     * @param   string path
     * @return  &io.Stream file
     */
    function &getFile($path) {
      if (isset ($this->fileMap[$path]))
        return $this->fileMap[$path];
    }
  }
?>
