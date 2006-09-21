<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Iteration filter
   *
   * @see      xp://io.folder.iterate.FilteredFolderIterator
   * @purpose  Interface
   */
  class IterationFilter extends Interface {
  
    /**
     * Accepts an element
     *
     * @access  public
     * @param   &io.collections.IOElement element
     * @return  bool
     */
    function accept(&$element) { }
  
  }
?>
