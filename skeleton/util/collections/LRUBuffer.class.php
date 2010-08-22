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
   * @test     xp://net.xp_framework.unittest.util.collections.LRUBufferTest
   * @test     xp://net.xp_framework.unittest.util.collections.GenericsTest
   * @purpose  Abstract data type
   */
  #[@generic(self= 'T')]
  class LRUBuffer extends Object {
    protected
      $size      = 0,
      $_access   = array(),
      $_elements = array();

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
     * @param   T element
     * @return  T victim
     */
    #[@generic(params= 'T', return= 'T')]
    public function add($element) {
      $h= $element instanceof Generic ? $element->hashCode() : $element;
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
     * @param   T element
     */
    #[@generic(params= 'T')]
    public function update($element) {
      $h= $element instanceof Generic ? $element->hashCode() : $element;
      $this->_access[$h]= microtime(TRUE);
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

    /**
     * Checks if a specified object is equal to this object.
     *
     * @param   lang.Generic collection
     * @return  bool
     */
    public function equals($cmp) {
      return (
        $cmp instanceof self &&
        $this->size === $cmp->size &&
        $this->__generic === $cmp->__generic &&
        $this->_access === $cmp->_access
      );
    }
  }
?>
