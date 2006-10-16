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
  class CreatedBeforeFilter extends AbstractDateComparisonFilter {

    /**
     * Accepts an element
     *
     * @access  public
     * @param   &io.collections.IOElement element
     * @return  bool
     */
    function accept(&$element) { 
      return ($cmp= &$element->createdAt()) && $cmp->isBefore($this->date);
    }
  }
?>
