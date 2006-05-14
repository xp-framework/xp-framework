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
   * @purpose  Abstract data type
   */
  class LRUBuffer extends Object {
    var
      $size = 0;

    var
      $_access   = array(),
      $_elements = array();
    
    /**
     * Constructor
     *
     * @access  public
     * @param   int size
     * @throws  lang.IllegalArgumentException is size is not greater than zero
     */
    function __construct($size) {
      $this->setSize($size);
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
     *   $deleted= &$buf->add($key);
     * </code>
     *
     * @access  public
     * @param   &lang.Object element
     * @return  &lang.Object victim
     */
    function &add(&$element) {
      $h= $element->hashCode();
      $this->_access[$h]= $this->microtime();
      $this->_elements[$h]= &$element;

      // Check if this buffer's size has been exceeded
      if (sizeof($this->_access) <= $this->size) return NULL;
      
      // Find the position of the smallest value and delete it
      $p= array_search(min($this->_access), $this->_access, TRUE);
      $victim= &$this->_elements[$p];

      unset($this->_access[$p]);
      unset($this->_elements[$p]);

      return $victim;
    }
    
    /**
     * Update an element
     *
     * @access  public
     * @param   &lang.Object element
     */
    function update(&$element) {
      $this->_access[$element->hashCode()]= $this->microtime();
    }
    
    /**
     * Get number of elements currently contained in this buffer
     *
     * @access  public
     * @return  int
     */
    function numElements() {
      return sizeof($this->_access);
    }
    
    /**
     * Set size
     *
     * @access  public
     * @param   int size
     * @throws  lang.IllegalArgumentException is size is not greater than zero
     */
    function setSize($size) {
      if ($size <= 0) return throw(new IllegalArgumentException(
        'Size must be greater than zero, '.$size.' given'
      ));

      $this->size= $size;
    }

    /**
     * Get size
     *
     * @access  public
     * @return  int
     */
    function getSize() {
      return $this->size;
    }
  }
?>
