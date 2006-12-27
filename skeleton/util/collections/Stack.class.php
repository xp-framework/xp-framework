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
   *   with ($s= &new Stack()); {
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
   * @see      http://www.faqs.org/docs/javap/c12/ex-12-1-answer.html
   * @see      http://java.sun.com/j2se/1.4.2/docs/api/java/util/Stack.html 
   */
  class Stack extends Object {
    public
      $_elements = array(),
      $_hash     = 0;
  
    /**
     * Pushes an item onto the top of the stack. Returns the element that 
     * was added.
     *
     * @param   &lang.Object object
     * @return  &lang.Object object
     */
    public function push($object) {
      array_unshift($this->_elements, $object);
      $this->_hash+= HashProvider::hashOf($object->hashCode());
      return $object;
    }

    /**
     * Gets an item from the top of the stack
     *
     * @return  &lang.Object
     * @throws  util.NoSuchElementException
     */    
    public function pop() {
      if (empty($this->_elements)) {
        throw(new NoSuchElementException('Stack is empty'));
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
     * @return  &lang.Object object
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
     * @param   &lang.Object object
     * @return  int position
     */
    public function search($object) {
      return ($keys= array_keys($this->_elements, $object)) ? $keys[0] : -1;
    }
    
    /**
     * Retrieves an element by its index.
     *
     * @param   int index
     * @return  &lang.Object
     * @throws  lang.IndexOutOfBoundsException
     */
    public function elementAt($index) {
      if (!isset($this->_elements[$index])) {
        throw(new IndexOutOfBoundsException('Index '.$index.' out of bounds'));
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
     * @param   &lang.Object cmp
     * @return  bool
     */
    public function equals($cmp) {
      return (
        is('util.collections.Stack', $cmp) && 
        ($this->hashCode() === $cmp->hashCode())
      );
    }
  }
?>
