<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('io.collections.iterate.AbstractSizeComparisonFilter');

  /**
   * Size comparison filter
   *
   * @purpose  Iteration Filter
   */
  class SizeSmallerThanFilter extends AbstractSizeComparisonFilter {

    /**
     * Accepts an element
     *
     * @access  public
     * @param   &io.collections.IOElement element
     * @return  bool
     */
    function accept(&$element) {
      return $element->getSize() < $this->size;
    }
  }
?>
