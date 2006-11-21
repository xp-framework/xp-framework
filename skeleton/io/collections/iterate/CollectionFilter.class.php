<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Filter that accepts only IOCollections (e.g. directories)
   *
   * @purpose  Iteration Filter
   */
  class CollectionFilter extends Object {
      
    /**
     * Accepts an element
     *
     * @access  public
     * @param   &io.collections.IOElement element
     * @return  bool
     */
    function accept(&$element) {
      return is('io.collections.IOCollection', $element);
    }

    /**
     * Creates a string representation of this iterator
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return $this->getClassName();
    }
  
  } implements(__FILE__, 'io.collections.iterate.IterationFilter');
?>
