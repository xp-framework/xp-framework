<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.NoSuchElementException');

  /**
   * Iterates over elements of a collection.
   *
   * @purpose  Interface
   */
  interface Iterator {
  
    /**
     * Returns true if the iteration has more elements. (In other words, 
     * returns true if next would return an element rather than throwing 
     * an exception.)
     *
     * @access  public
     * @return  bool
     */
    public function hasNext();
    
    /**
     * Returns the next element in the iteration.
     *
     * @access  public
     * @return  &mixed
     * @throws  util.NoSuchElementException when there are no more elements
     */
    public function &next();
  }
?>
