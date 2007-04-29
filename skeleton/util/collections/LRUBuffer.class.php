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
    protected
      $size      = 0,
      $_access   = array(),
      $_elements = array();

    public
      $__generic = array();
    
    /**
     * Constructor
     *
     * @param   int size
     * @throws  lang.IllegalArgumentException is size is not greater than zero
     */
    public function __construct($size) {
      $this->setSize($size);
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
     * @param   lang.Generic element
     * @return  lang.Generic victim
     */
    public function add(Generic $element) {
      if ($this->__generic && !$element instanceof $this->__generic[0]) {
        throw new IllegalArgumentException('Element '.xp::stringOf($element).' must be of '.$this->__generic[0]);
      }
      $h= $element->hashCode();
      $this->_access[$h]= microtime(TRUE);
      $this->_elements[$h]= $element;

      // Check if this buffer's size has been exceeded
      if (sizeof($this->_access) <= $this->size) return NULL;
      
      // Find the position of the smallest value and delete it
      $p= array_search(min($this->_access), $this->_access, TRUE);
      $victim= $this->_elements[$p];

      unset($this->_access[$p]);
      unset($this->_elements[$p]);

      return $victim;
    }
    
    /**
     * Update an element
     *
     * @param   lang.Generic element
     */
    public function update(Generic $element) {
      if ($this->__generic && !$element instanceof $this->__generic[0]) {
        throw new IllegalArgumentException('Element '.xp::stringOf($element).' must be of '.$this->__generic[0]);
      }
      $this->_access[$element->hashCode()]= microtime(TRUE);
    }
    
    /**
     * Get number of elements currently contained in this buffer
     *
     * @return  int
     */
    public function numElements() {
      return sizeof($this->_access);
    }
    
    /**
     * Set size
     *
     * @param   int size
     * @throws  lang.IllegalArgumentException is size is not greater than zero
     */
    public function setSize($size) {
      if ($size <= 0) throw new IllegalArgumentException(
        'Size must be greater than zero, '.$size.' given'
      );

      $this->size= $size;
    }

    /**
     * Get size
     *
     * @return  int
     */
    public function getSize() {
      return $this->size;
    }
  }
?>
