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
   * A Last-In-First-Out (LIFO) stack of objects.
   *
   * Example:
   * <code>
   *   uses('util.collections.Stack', 'text.String');
   *   
   *   // Fill stack
   *   with ($s= new Stack()); {
   *     $s->push(new String('One'));
   *     $s->push(new String('Two'));
   *     $s->push(new String('Three'));
   *     $s->push(new String('Four'));
   *   }
   *   
   *   // Empty stack
   *   while (!$s->isEmpty()) {
   *     var_dump($s->pop());
   *   }
   * </code>
   *
   * @purpose  LIFO
   * @see      xp://util.collections.Queue
   * @test     xp://net.xp_framework.unittest.util.collections.GenericsTest
   * @test     xp://net.xp_framework.unittest.util.collections.StackTest
   * @see      http://www.faqs.org/docs/javap/c12/ex-12-1-answer.html
   * @see      http://java.sun.com/j2se/1.4.2/docs/api/java/util/Stack.html 
   */
  class Stack extends Object {
    protected
      $_elements = array(),
      $_hash     = 0;

    public
      $__generic = array();
  
    /**
     * Pushes an item onto the top of the stack. Returns the element that 
     * was added.
     *
     * @param   lang.Generic object
     * @return  lang.Generic object
     */
    public function push(Generic $object) {
      if ($this->__generic && !$object instanceof $this->__generic[0]) {
        throw new IllegalArgumentException('Object '.xp::stringOf($object).' must be of '.$this->__generic[0]);
      }
      array_unshift($this->_elements, $object);
      $this->_hash+= HashProvider::hashOf($object->hashCode());
      return $object;
    }

    /**
     * Gets an item from the top of the stack
     *
     * @return  lang.Generic
     * @throws  util.NoSuchElementException
     */    
    public function pop() {
      if (empty($this->_elements)) {
        throw new NoSuchElementException('Stack is empty');
      }
      $element= array_shift($this->_elements);
      $this->_hash+= HashProvider::hashOf($element->hashCode());
      return $element;
    }

    /**
     * Peeks at the front of the stack (retrieves the first element 
     * without removing it).
     *
     * Returns NULL in case the stack is empty.
     *
     * @return  lang.Generic object
     */        
    public function peek() {
      if (empty($this->_elements)) return NULL; else return $this->_elements[0];
    }
  
    /**
     * Returns true if the stack is empty. This is effectively the same
     * as testing size() for 0.
     *
     * @return  bool
     */
    public function isEmpty() {
      return empty($this->_elements);
    }

    /**
     * Returns the size of the stack.
     *
     * @return  int
     */
    public function size() {
      return sizeof($this->_elements);
    }
    
    /**
     * Sees if an object is in the stack and returns its position.
     * Returns -1 if the object is not found.
     *
     * @param   lang.Generic object
     * @return  int position
     */
    public function search(Generic $object) {
      if ($this->__generic && !$object instanceof $this->__generic[0]) {
        throw new IllegalArgumentException('Object '.xp::stringOf($object).' must be of '.$this->__generic[0]);
      }
      return ($keys= array_keys($this->_elements, $object)) ? $keys[0] : -1;
    }
    
    /**
     * Retrieves an element by its index.
     *
     * @param   int index
     * @return  lang.Generic
     * @throws  lang.IndexOutOfBoundsException
     */
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
