<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * LRU (last recently used) buffer.
   *
   * The last recently used (that is, the longest time unchanged) 
   * element will be deleted when calling add().
   *
   * @deprecated by RFC #0057
   * @purpose  Abstract data type
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
     * Add an element to the buffer and return the id of the element 
     * which has been deleted in exchange. Returns NULL for the case 
     * that no element has been deleted (which is the case when the 
     * buffer's size has not yet been exceeded).
     *
     * <code>
     *   $deleted= $buf->add($key);
     * </code>
     *
     * @access  public
     * @param   string key
     * @return  int
     */
    function add($id) {
      $this->_buf[$id]= $this->microtime();
      if (sizeof($this->_buf) > $this->size) {
      
        // Find the position of the smallest value and delete it
        $p= array_search(min($this->_buf), $this->_buf, TRUE);
        unset($this->_buf[$p]);
        return $p;
      }

      return NULL;
    }
    
    /**
     * Update an element
     *
     * @access  public
     * @param   string id
     */
    function update($id) {
      $this->_buf[$id]= $this->microtime();
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
