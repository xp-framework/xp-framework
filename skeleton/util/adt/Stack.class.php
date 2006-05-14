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
   * @deprecated by RFC #0057
   * @purpose  LIFO
   * @see      xp://util.adt.Queue
   * @see      http://www.faqs.org/docs/javap/c12/ex-12-1-answer.html
   * @see      http://java.sun.com/j2se/1.4.2/docs/api/java/util/Stack.html 
   */
  class Stack extends Object {
    var
      $_elements= array();
  
    /**
     * Pushes an item onto the top of the stack. Returns the element that 
     * was added.
     *
     * @access  public
     * @param   &lang.Object object
     * @return  &lang.Object object
     */
    function &push(&$object) {
      array_unshift($this->_elements, $object);
      return $object;
    }

    /**
     * Gets an item from the top of the stack
     *
     * @access  public
     * @return  &lang.Object
     * @throws  util.NoSuchElementException
     */    
    function &pop() {
      if (empty($this->_elements)) {
        return throw(new NoSuchElementException('Stack is empty'));
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
     * @return  &lang.Object object
     */        
    function &peek() {
      if (empty($this->_elements)) return NULL; else return $this->_elements[0];
    }
  
    /**
     * Returns true if the stack is empty. This is effectively the same
     * as testing size() for 0.
     *
     * @access  public
     * @return  bool
     */
    function isEmpty() {
      return empty($this->_elements);
    }

    /**
     * Returns the size of the stack.
     *
     * @access  public
     * @return  int
     */
    function size() {
      return sizeof($this->_elements);
    }
    
    /**
     * Sees if an object is in the stack and returns its position.
     * Returns -1 if the object is not found.
     *
     * @access  public
     * @param   &lang.Object object
     * @return  int position
     */
    function search(&$object) {
      return ($keys= array_keys($this->_elements, $object)) ? $keys[0] : -1;
    }
    
    /**
     * Retrieves an element by its index.
     *
     * @access  public
     * @param   int index
     * @return  &lang.Object
     * @throws  lang.IndexOutOfBoundsException
     */
    function &elementAt($index) {
      if (!isset($this->_elements[$index])) {
        return throw(new IndexOutOfBoundsException('Index '.$index.' out of bounds'));
      }
      return $this->_elements[$index];
    }
  }
?>
