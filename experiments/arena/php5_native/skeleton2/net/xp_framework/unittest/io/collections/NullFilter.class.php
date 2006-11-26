<?php
/* This class is part of the XP framework
 *
 * $Id: NullFilter.class.php 8051 2006-10-03 16:00:33Z friebe $
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
     * @access  public
     * @param   &io.collections.IOElement element
     * @return  bool
     */
    public function accept(&$element) {
      return TRUE;
    }
  
  } 
?>
