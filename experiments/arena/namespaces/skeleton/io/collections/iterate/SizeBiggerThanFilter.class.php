<?php
/* This class is part of the XP framework
 *
 * $Id: SizeBiggerThanFilter.class.php 10594 2007-06-11 10:04:54Z friebe $
 */

  namespace io::collections::iterate;

  ::uses('io.collections.iterate.AbstractSizeComparisonFilter');

  /**
   * Size comparison filter
   *
   * @purpose  Iteration Filter
   */
  class SizeBiggerThanFilter extends AbstractSizeComparisonFilter {

    /**
     * Accepts an element
     *
     * @param   io.collections.IOElement element
     * @return  bool
     */
    public function accept($element) {
      return $element->getSize() > $this->size;
    }
  }
?>
