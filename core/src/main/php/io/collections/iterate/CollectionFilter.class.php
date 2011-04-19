<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('io.collections.iterate.IterationFilter');

  /**
   * Filter that accepts only IOCollections (e.g. directories)
   *
   * @purpose  Iteration Filter
   */
  class CollectionFilter extends Object implements IterationFilter {
      
    /**
     * Accepts an element
     *
     * @param   io.collections.IOElement element
     * @return  bool
     */
    public function accept($element) {
      return is('io.collections.IOCollection', $element);
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
