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
      $prefix    = 0,
      $size      = 0,
      $_elements = array();

    /**
     * Constructor
     *
     * @param   int size
     * @throws  lang.IllegalArgumentException is size is not greater than zero
     */
    public function __construct($size) {
      static $u;

      $this->prefix= ++$u;
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
      $h= $this->prefix.($element instanceof Generic ? $element->hashCode() : serialize($element));
      $this->_elements[$h]= $element;

      // Check if this buffer's size has been exceeded
      if (sizeof($this->_elements) <= $this->size) return NULL;
      
      // Delete the element first added
      $p= key($this->_elements);
      $victim= $this->_elements[$p];
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
      $h= $this->prefix.($element instanceof Generic ? $element->hashCode() : serialize($element));
      unset($this->_elements[$h]);
      $this->_elements= $this->_elements + array($h => $element);
    }
    
    /**
     * Get number of elements currently contained in this buffer
     *
     * @return  int
     */
    public function numElements() {
      return sizeof($this->_elements);
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
        array_keys($this->_elements) === array_keys($cmp->_elements)
      );
    }
  }
?>
