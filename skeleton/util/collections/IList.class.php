<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.IndexOutOfBoundsException');

  /**
   * Interface for lists
   *
   * @purpose  Interface
   */
  interface IList extends ArrayAccess, IteratorAggregate {

    /**
     * Returns the number of elements in this list.
     *
     * @return  int
     */
    public function size();
    
    /**
     * Tests if this list has no elements.
     *
     * @return  bool
     */
    public function isEmpty();

    /**
     * Adds an element to this list
     *
     * @param   lang.Generic element
     * @return  lang.Generic the added element
     * @throws  lang.IllegalArgumentException
     */
    public function add(Generic $element);

    /**
     * Replaces the element at the specified position in this list with 
     * the specified element.
     *
     * @param   int index
     * @param   lang.Generic element
     * @return  lang.Generic the element previously at the specified position.
     */
    public function set($index, Generic $element);

    /**
     * Returns the element at the specified position in this list.
     *
     * @param   int index
     * @return  lang.Generic
     * @throws  lang.IndexOutOfBoundsException if key does not exist
     */
    public function get($index);
 
    /**
     * Removes the element at the specified position in this list.
     * Shifts any subsequent elements to the left (subtracts one 
     * from their indices).
     *
     * @param   int index
     * @return  lang.Generic the element that was removed from the list
     */
    public function remove($index);

    /**
     * Checks if a value exists in this list
     *
     * @param   lang.Generic element
     * @return  bool
     */
    public function contains(Generic $element);

    /**
     * Removes all of the elements from this list. The list will be empty 
     * after this call returns.
     *
     */
    public function clear();
 

  }
?>
