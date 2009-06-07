<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('io.collections.iterate.AbstractCombinedFilter');

  /**
   * Combined filter that accepts an element if all of its filters
   * accept the element.
   *
   * This filter:
   * <code>
   *   $filter= new AllOfFilter(array(
   *     new ModifiedBeforeFilter(new Date('Dec 14  2004')),
   *     new ExtensionEqualsFilter('jpg')
   *   ));
   * </code>
   * will accept all elements modified before Dec 14  2004 AND whose
   * extension is ".jpg"
   *
   * @purpose  Iteration Filter
   */
  class AllOfFilter extends AbstractCombinedFilter {
    
    /**
     * Accepts an element
     *
     * @param   io.collections.IOElement element
     * @return  bool
     */
    public function accept($element) {
      for ($i= 0; $i < $this->_size; $i++) {
      
        // The first filter that does not accept the element => we won't accept the element
        if (!$this->list[$i]->accept($element)) return FALSE;
      }
      
      // All filters have accepted the element, so we accept it
      return TRUE;
    }
  }
?>
