<?php
/* This class is part of the XP framework's people experiments
 *
 * $Id$
 */

  /**
   * An iterator over a collection
   *
   * @purpose  Interface
   */
  class Iterator {
  
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
