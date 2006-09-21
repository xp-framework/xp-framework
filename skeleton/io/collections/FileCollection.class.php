<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.collections.FileElement');

  /**
   * File collection
   *
   * @see      xp://io.collections.IOCollection
   * @purpose  IOCollection implementation
   */
  class FileCollection extends Object {
    var
      $uri = '';
    
    var
      $_hd = NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string uri
     */
    function __construct($uri) {
      $this->uri= rtrim(realpath($uri), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
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
     * Open this collection
     *
     * @access  public
     */
    function open() { 
      $this->_hd= opendir($this->uri);
    }

    /**
     * Rewind this collection (reset internal pointer to beginning of list)
     *
     * @access  public
     */
    function rewind() { 
      rewinddir($this->_hd);
    }
  
    /**
     * Retrieve next element in collection. Return NULL if no more entries
     * are available
     *
     * @access  public
     * @return  &io.collection.IOElement
     */
    function &next() { 
      do {
        if (FALSE === ($entry= readdir($this->_hd))) return NULL;
      } while ('.' == $entry || '..' == $entry);
      
      $qualified= $this->uri.$entry; 
      if (is_dir($qualified)) {
        $next= &new FileCollection($qualified);
      } else {
        $next= &new FileElement($qualified);
      }
      return $next;
    }

    /**
     * Close this collection
     *
     * @access  public
     */
    function close() { 
      closedir($this->_hd);
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
  
  } implements(__FILE__, 'io.collections.IOCollection');
?>
