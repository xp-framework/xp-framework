<?php
/* This class is part of the XP framework
 *
 * $Id: CreatedBeforeFilter.class.php 8185 2006-10-16 10:24:01Z friebe $
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
    public function accept(&$element) { 
      return ($cmp= &$element->createdAt()) && $cmp->isBefore($this->date);
    }
  }
?>
