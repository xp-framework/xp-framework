<?php
/* This class is part of the XP framework
 *
 * $Id: CollectionFilter.class.php 8540 2006-11-21 01:51:58Z kiesel $
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
     * @access  public
     * @param   &io.collections.IOElement element
     * @return  bool
     */
    public function accept(&$element) {
      return is('io.collections.IOCollection', $element);
    }

    /**
     * Creates a string representation of this iterator
     *
     * @access  public
     * @return  string
     */
    public function toString() {
      return $this->getClassName();
    }
  
  } 
?>
