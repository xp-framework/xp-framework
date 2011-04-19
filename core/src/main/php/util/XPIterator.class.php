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
  interface XPIterator {
  
    /**
     * Returns true if the iteration has more elements. (In other words, 
     * returns true if next would return an element rather than throwing 
     * an exception.)
     *
     * @return  bool
     */
    public function hasNext();
    
    /**
     * Returns the next element in the iteration.
     *
     * @return  var
     * @throws  util.NoSuchElementException when there are no more elements
     */
    public function next();
  }
?>
