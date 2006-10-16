<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('io.collections.iterate.AbstractDateComparisonFilter');

  /**
   * Date comparison filter
   *
   * @purpose  Iteration Filter
   */
  class AccessedBeforeFilter extends AbstractDateComparisonFilter {

    /**
     * Accepts an element
     *
     * @access  public
     * @param   &io.collections.IOElement element
     * @return  bool
     */
    function accept(&$element) { 
      return ($cmp= &$element->lastAccessed()) && $cmp->isBefore($this->date);
    }
  }
?>
