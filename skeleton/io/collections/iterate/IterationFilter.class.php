<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Iteration filter
   *
   * @see      xp://io.collections.iterate.FilteredIOCollectionIterator
   * @purpose  Interface
   */
  interface IterationFilter {
  
    /**
     * Accepts an element
     *
     * @param   io.collections.IOElement element
     * @return  bool
     */
    public function accept($element);
  
  }
?>
