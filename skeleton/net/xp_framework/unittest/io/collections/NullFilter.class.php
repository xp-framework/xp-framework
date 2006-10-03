<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Accept-all filter
   *
   * @purpose  Iteration Filter
   */
  class NullFilter extends Object {
  
    /**
     * Accepts an element
     *
     * @access  public
     * @param   &io.collections.IOElement element
     * @return  bool
     */
    function accept(&$element) {
      return TRUE;
    }
  
  } implements(__FILE__, 'io.collections.iterate.IterationFilter');
?>
