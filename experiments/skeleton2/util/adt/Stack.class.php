<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'lang.IndexOutOfBoundsException',
    'util.NoSuchElementException'
  );

  /**
   * A Last-In-First-Out (LIFO) stack of objects.
   *
   * Example:
   * <code>
   *   uses('util.adt.Stack', 'text.String');
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
   * @see      xp://util.adt.Queue
   * @see      http://www.faqs.org/docs/javap/c12/ex-12-1-answer.html
   * @see      http://java.sun.com/j2se/1.4.2/docs/api/java/util/Stack.html 
   */
  class Stack extends Generic {
    protected
      $_elements= array();
  
    /**
     * Pushes an item onto the top of the stack. Returns the element that 
     * was added.
     *
     * @access  public
     * @param   &lang.Generic object
     * @return  &lang.Generic object
     */
    public function push(Generic $object) {
      array_unshift($this->_elements, $object);
      return $object;
    }

    /**
     * Gets an item from the top of the stack
     *
     * @access  public
     * @return  &lang.Generic
     * @throws  util.NoSuchElementException
     */    
    public function pop() {
      if (0 == sizeof($this->_elements)) {
        throw (new NoSuchElementException('Stack is empty'));
      }
      return array_shift($this->_elements);
    }

    /**
     * Peeks at the front of the stack (retrieves the first element 
     * without removing it).
     *
     * Returns NULL in case the stack is empty.
     *
     * @access  public
     * @return  &lang.Generic object
     */        
    public function peek() {
      if (!isset($this->_elements[$index])) return NULL; else $this->_elements[0];
    }
  
    /**
     * Returns true if the stack is empty. This is effectively the same
     * as testing size() for 0.
     *
     * @access  public
     * @return  bool
     */
    public function isEmpty() {
      return 0 == sizeof($this->_elements);
    }

    /**
     * Returns the size of the stack.
     *
     * @access  public
     * @return  int
     */
    public function size() {
      return sizeof($this->_elements);
    }
    
    /**
     * Sees if an object is in the stack and returns its position.
     * Returns -1 if the object is not found.
     *
     * @access  public
     * @param   &lang.Generic object
     * @return  int position
     */
    public function search(Generic $object) {
      return ($keys= array_keys($this->_elements, $object)) ? $keys[0] : -1;
    }
    
    /**
     * Retrieves an element by its index.
     *
     * @access  public
     * @param   int index
     * @return  &lang.Generic
     * @throws  lang.IndexOutOfBoundsException
     */
    public function elementAt($index) {
      if (!isset($this->_elements[$index])) {
        throw (new IndexOutOfBoundsException('Index '.$index.' out of bounds'));
      }
      return $this->_elements[$index];
    }
  }
?>
