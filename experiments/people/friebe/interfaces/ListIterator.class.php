<?php
/* This class is part of the XP framework's people experiments
 *
 * $Id$
 */

  /**
   * List iterator
   *
   * @purpose  Iterator
   */
  class ListIterator extends Object {
  
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
  
  } implements('Iterator');
?>
