<?php
/* This class is part of the XP framework
 *
 * $Id: NullFilter.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace net::xp_framework::unittest::io::collections;

  ::uses('io.collections.iterate.IterationFilter');

  /**
   * Accept-all filter
   *
   * @purpose  Iteration Filter
   */
  class NullFilter extends lang::Object implements io::collections::iterate::IterationFilter {
  
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
