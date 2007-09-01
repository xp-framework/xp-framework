<?php
/* This class is part of the XP framework
 *
 * $Id: CollectionFilter.class.php 10594 2007-06-11 10:04:54Z friebe $
 */

  namespace io::collections::iterate;

  ::uses('io.collections.iterate.IterationFilter');

  /**
   * Filter that accepts only IOCollections (e.g. directories)
   *
   * @purpose  Iteration Filter
   */
  class CollectionFilter extends lang::Object implements IterationFilter {
      
    /**
     * Accepts an element
     *
     * @param   io.collections.IOElement element
     * @return  bool
     */
    public function accept($element) {
      return ::is('io.collections.IOCollection', $element);
    }

    /**
     * Creates a string representation of this iterator
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName();
    }
  
  } 
?>
