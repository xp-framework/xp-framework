<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents a file element
   *
   * @see      xp://io.collections.FileCollection
   * @purpose  Interface
   */
  class FileElement extends Object {
    var
      $uri= '';

    /**
     * Constructor
     *
     * @access  publid
     * @param   string uri
     */
    function __construct($uri) {
      $this->uri= $uri;
    }

    /**
     * Returns this element's URI
     *
     * @access  public
     * @return  string
     */
    function getURI() { 
      return $this->uri;
    }

    /**
     * Retrieve this element's size in bytes
     *
     * @access  public
     * @return  int
     */
    function getSize() { 
      return filesize($this->uri);
    }

    /**
     * Retrieve this element's created date and time
     *
     * @access  public
     * @return  &util.Date
     */
    function &createdAt() {
      return new Date(filectime($this->uri));
    }

    /**
     * Retrieve this element's last-accessed date and time
     *
     * @access  public
     * @return  &util.Date
     */
    function &lastAccessed() {
      return new Date(fileatime($this->uri));
    }

    /**
     * Retrieve this element's last-modified date and time
     *
     * @access  public
     * @return  &util.Date
     */
    function &lastModified() {
      return new Date(filemtime($this->uri));
    }
    
    /**
     * Creates a string representation of this object
     *
     * @access  public
     * @return  string
     */
    function toString() { 
      return $this->getClassName().'('.$this->uri.')';
    }

  } implements(__FILE__, 'io.collections.IOElement');
?>
