<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('io.collections.iterate.IterationFilter');

  /**
   * Accept-all filter
   *
   * @purpose  Iteration Filter
   */
  class NullFilter extends Object implements IterationFilter {
  
    /**
     * Accepts an element
     *
     * @param   &io.collections.IOElement element
     * @return  bool
     */
    public function accept($element) {
      return TRUE;
    }
  
  } 
?>
