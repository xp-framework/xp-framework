<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('ListIterator');

  /**
   * Sorted list iterator
   *
   * @purpose  Iterator
   */
  class SortedListIterator extends ListIterator {
  
    /**
     * Get next element
     *
     * @access  public
     * @return  &mixed
     */
    function &next() { }
    
    /**
     * Retrieve whether there are more elements
     *
     * @access  public
     * @return  bool
     */
    function hasNext() { }
  
  }
?>
