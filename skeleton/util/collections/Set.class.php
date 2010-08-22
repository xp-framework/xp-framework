<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * A set of objects
   *
   * @purpose  Interface
   */
  #[@generic(self= 'T')]
  interface Set extends ArrayAccess, IteratorAggregate {
  
    /**
     * Adds an object
     *
     * @param   T object
     * @return  bool TRUE if this set did not already contain the specified element. 
     */
    #[@generic(params= 'T')]
    public function add($element);

    /**
     * Removes an object from this set
     *
     * @param   T element
     * @return  bool TRUE if this set contained the specified element. 
     */
    #[@generic(params= 'T')]
    public function remove($element);

    /**
     * Removes an object from this set
     *
     * @param   T element
     * @return  bool TRUE if the set contains the specified element. 
     */
    #[@generic(params= 'T')]
    public function contains($element);

    /**
     * Returns this set's size
     *
     * @return  int
     */
    public function size();

    /**
     * Removes all of the elements from this set
     *
     */
    public function clear();

    /**
     * Returns whether this set is empty
     *
     * @return  bool
     */
    public function isEmpty();

    /**
     * Adds an array of objects
     *
     * @param   T[] elements
     * @return  bool TRUE if this set changed as a result of the call. 
     */
    #[@generic(params= 'T[]')]
    public function addAll($elements);

    /**
     * Returns an array containing all of the elements in this set. 
     *
     * @return  T[] elements
     */
    #[@generic(return= 'T[]')]
    public function toArray();

    /**
     * Returns a hashcode for this set
     *
     * @return  string
     */
    public function hashCode();
    
    /**
     * Returns true if this set equals another set.
     *
     * @param   lang.Generic cmp
     * @return  bool
     */
    public function equals($cmp);
  }
?>
