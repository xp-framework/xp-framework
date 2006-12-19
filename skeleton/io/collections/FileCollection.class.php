<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.collections.FileElement', 'io.collections.IOCollection');

  /**
   * File collection
   *
   * @see      xp://io.collections.IOCollection
   * @purpose  IOCollection implementation
   */
  class FileCollection extends Object implements IOCollection {
    public
      $uri = '';
    
    public
      $_hd = NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string uri
     */
    public function __construct($uri) {
      $this->uri= rtrim(realpath($uri), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
    }
    
    /**
     * Returns this element's URI
     *
     * @access  public
     * @return  string
     */
    public function getURI() {
      return $this->uri;
    }
    
    /**
     * Open this collection
     *
     * @access  public
     */
    public function open() { 
      $this->_hd= opendir($this->uri);
    }

    /**
     * Rewind this collection (reset internal pointer to beginning of list)
     *
     * @access  public
     */
    public function rewind() { 
      rewinddir($this->_hd);
    }
  
    /**
     * Retrieve next element in collection. Return NULL if no more entries
     * are available
     *
     * @access  public
     * @return  &io.collection.IOElement
     */
    public function &next() { 
      do {
        if (FALSE === ($entry= readdir($this->_hd))) return NULL;
      } while ('.' == $entry || '..' == $entry);
      
      $qualified= $this->uri.$entry; 
      if (is_dir($qualified)) {
        $next= new FileCollection($qualified);
      } else {
        $next= new FileElement($qualified);
      }
      return $next;
    }

    /**
     * Close this collection
     *
     * @access  public
     */
    public function close() { 
      closedir($this->_hd);
    }

    /**
     * Retrieve this element's size in bytes
     *
     * @access  public
     * @return  int
     */
    public function getSize() { 
      return filesize($this->uri);
    }

    /**
     * Retrieve this element's created date and time
     *
     * @access  public
     * @return  &util.Date
     */
    public function &createdAt() {
      return new Date(filectime($this->uri));
    }

    /**
     * Retrieve this element's last-accessed date and time
     *
     * @access  public
     * @return  &util.Date
     */
    public function &lastAccessed() {
      return new Date(fileatime($this->uri));
    }

    /**
     * Retrieve this element's last-modified date and time
     *
     * @access  public
     * @return  &util.Date
     */
    public function &lastModified() {
      return new Date(filemtime($this->uri));
    }

    /**
     * Creates a string representation of this object
     *
     * @access  public
     * @return  string
     */
    public function toString() { 
      return $this->getClassName().'('.$this->uri.')';
    }
  
  } 
?>
