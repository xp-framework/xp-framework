<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * LRU (last recently used) buffer.
   *
   * The last recently used (that is, the oldest) element will
   * be deleted when calling add(). An element will be refreshed
   * by calling update().
   *
   * @purpose  Buffer
   */
  class LRUBuffer extends Object {
    var
      $size = 0;

    var
      $_buf = array();
    
    /**
     * Constructor
     *
     * @access  public
     * @param   int size
     */
    function __construct($size) {
      $this->size= $size;
    }
    
    /**
     * Retrieve current microtime
     *
     * @access  public
     * @return  float microtime
     */
    function microtime() {
      list($usec, $sec)= explode(' ', microtime());
      return (float)$usec + (float)$sec;
    }
    
    /**
     * Add an element to the buffer and return the element which has
     * been deleted in exchange. Returns -1 for the case that no 
     * element has been shifted (which is the case when the buffer's
     * size has not yet been exceeded)
     *
     * @access  public
     * @return  int
     */
    function add() {
      $this->_buf[]= $this->microtime();
      if (sizeof($this->_buf) > $this->size) {
      
        // Find the position of the smallest value and delete it
        $p= array_search(min($this->_buf), $this->_buf, TRUE);
        unset($this->_buf[$p]);
        return $p;
      }
      return -1;
    }
    
    /**
     * Update an entry
     *
     * @access  public
     * @param   int element
     */
    function update($element) {
      $this->_buf[$element]= $this->microtime();
    }
    
    /**
     * Set Size
     *
     * @access  public
     * @param   int size
     */
    function setSize($size) {
      $this->size= $size;
    }

    /**
     * Get Size
     *
     * @access  public
     * @return  int
     */
    function getSize() {
      return $this->size;
    }
  }
?>
