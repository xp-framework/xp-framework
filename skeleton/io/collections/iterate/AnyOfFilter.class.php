<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('io.collections.iterate.AbstractCombinedFilter');

  /**
   * Combined filter that accepts an element if any of its filters
   * accept the element.
   *
   * This filter:
   * <code>
   *   $filter= &new AnyOfFilter(array(
   *     new SizeSmallerThanFilter(500),
   *     new ExtensionEqualsFilter('txt')
   *   ));
   * </code>
   * will accept any elements smaller than 500 bytes or with a
   * ".txt"-extension.
   *
   * @purpose  Iteration Filter
   */
  class AnyOfFilter extends AbstractCombinedFilter {
    
    /**
     * Accepts an element
     *
     * @param   io.collections.IOElement element
     * @return  bool
     */
    public function accept($element) {
      for ($i= 0; $i < $this->_size; $i++) {
      
        // The first filter that accepts the element => we accept the element
        if ($this->list[$i]->accept($element)) return TRUE;
      }
      
      // None of the filters have accepted the element, so we won't accept it
      return FALSE;
    }
  }
?>
