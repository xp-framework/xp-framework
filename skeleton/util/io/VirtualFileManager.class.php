<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('io.Stream');
  
  /**
   * A file manager singleton to provide access to 
   * not really existing files
   *
   * @purpose Provide access to nonexistant files
   * @deprecated Obsolete.
   */
  class VirtualFileManager extends Object {
    
    /**
     * Constructor
     *
     */
    public function __construct() {
      $this->fileMap= array();
    }
    
    /**
     * Gets the instance of the VirtualFileManager
     *
     * @return  io.VirtualFileManager
     */
    public function getInstance() {
      static $instance= NULL;
      
      if (NULL === $instance) $instance= new VirtualFileManager();
      return $instance;
    }
    
    /**
     * Adds a file
     *
     * @param   string path
     * @param   string data
     */
    public function addFile($path, $data) {
      $stream= new Stream();
      $stream->open (STREAM_MODE_READWRITE);
      $stream->write ($data);
      $stream->rewind();
      
      $this->fileMap[$path]= $data;
    }
    
    /**
     * Retrieves a file
     *
     * @param   string path
     * @return  io.Stream file
     */
    public function getFile($path) {
      if (isset ($this->fileMap[$path]))
        return $this->fileMap[$path];
    }
  }
?>
