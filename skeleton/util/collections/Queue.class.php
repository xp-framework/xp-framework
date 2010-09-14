<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'lang.IndexOutOfBoundsException',
    'util.NoSuchElementException',
    'util.collections.HashProvider'
  );

  /**
   * A First-In-First-Out (FIFO) queue of objects.
   *
   * Example:
   * <code>
   *   uses('util.collections.Queue', 'text.String');
   *   
   *   // Fill queue
   *   with ($q= new Queue()); {
   *     $q->put(new String('One'));
   *     $q->put(new String('Two'));
   *     $q->put(new String('Three'));
   *     $q->put(new String('Four'));
   *   }
   *   
   *   // Empty queue
   *   while (!$q->isEmpty()) {
   *     var_dump($q->get());
   *   }
   * </code>
   *
   * @purpose  FIFO
   * @test     xp://net.xp_framework.unittest.util.collections.GenericsTest
   * @test     xp://net.xp_framework.unittest.util.collections.QueueTest
   * @see      xp://util.collections.Stack
   * @see      http://www.faqs.org/docs/javap/c12/ex-12-1-answer.html
   */
  #[@generic(self= 'T')]
  class Queue extends Object {
    protected
      $_elements = array(),
      $_hash     = 0;

    /**
     * Puts an item into the queue. Returns the element that was added.
     *
     * @param   T element
     * @return  T element
     */
    #[@generic(params= 'T', return= 'T')]
    public function put($element) {
      $h= $element instanceof Generic ? $element->hashCode() : $element;
      $this->_elements[]= $element;
      $this->_hash+= HashProvider::hashOf($h);
      return $element;
    }

    /**
     * Gets an item from the front of the queue.
     *
     * @return  lang.Generic
     * @throws  util.NoSuchElementException
     */    
    #[@generic(return= 'T')]
    public function get() {
      if (empty($this->_elements)) {
        throw new NoSuchElementException('Queue is empty');
      }

      $element= $this->_elements[0];
      $h= $element instanceof Generic ? $element->hashCode() : $element;
      $this->_hash-= HashProvider::hashOf($h);
      $this->_elements= array_slice($this->_elements, 1);
      return $element;
    }
    
    /**
     * Peeks at the front of the queue (retrieves the first element 
     * without removing it).
     *
     * Returns NULL in case the queue is empty.
     *
     * @return  T element
     */        
    #[@generic(return= 'T')]
    public function peek() {
      if (empty($this->_elements)) return NULL; else return $this->_elements[0];
    }
  
    /**
     * Returns true if the queue is empty. This is effectively the same
     * as testing size() for 0.
     *
     * @return  bool
     */
    public function isEmpty() {
      return empty($this->_elements);
    }

    /**
     * Returns the size of the queue.
     *
     * @return  int
     */
    public function size() {
      return sizeof($this->_elements);
    }
    
    /**
     * Sees if an object is in the queue and returns its position.
     * Returns -1 if the object is not found.
     *
     * @param   T element
     * @return  int position
     */
    #[@generic(params= 'T')]
    public function search($element) {
      return ($keys= array_keys($this->_elements, $element)) ? $keys[0] : -1;
    }

    /**
     * Remove an object from the queue. Returns TRUE in case the element
     * was deleted, FALSE otherwise.
     *
     * @return  lang.Generic
     * @return  bool
     */
    #[@generic(params= 'T')]
    public function remove($element) {
      if (-1 == ($pos= $this->search($element))) return FALSE;
      
      $h= $this->_elements[$pos] instanceof Generic ? $this->_elements[$pos]->hashCode() : $this->_elements[$pos];
      $this->_hash-= HashProvider::hashOf($h);
      unset($this->_elements[$pos]);
      $this->_elements= array_values($this->_elements);   // Re-index
      return TRUE;
    }
    
    /**
     * Retrieves an element by its index.
     *
     * @param   int index
     * @return  T
     * @throws  lang.IndexOutOfBoundsException
     */
    #[@generic(return= 'T')]
    public function elementAt($index) {
      if (!isset($this->_elements[$index])) {
        throw new IndexOutOfBoundsException('Index '.$index.' out of bounds');
      }
      return $this->_elements[$index];
    }

    /**
     * Returns a hashcode for this queue
     *
     * @return  string
     */
    public function hashCode() {
      return $this->_hash;
    }
    
    /**
     * Returns true if this queue equals another queue.
     *
     * @param   lang.Generic cmp
     * @return  bool
     */
    public function equals($cmp) {
      return (
        $cmp instanceof self && 
        $this->__generic === $cmp->__generic &&
        $this->_hash === $cmp->_hash
      );
    }
  }
?>
