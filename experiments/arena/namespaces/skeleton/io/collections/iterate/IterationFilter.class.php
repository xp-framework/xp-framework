<?php
/* This class is part of the XP framework
 *
 * $Id: IterationFilter.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace io::collections::iterate;

  /**
   * Iteration filter
   *
   * @see      xp://io.folder.iterate.FilteredFolderIterator
   * @purpose  Interface
   */
  interface IterationFilter {
  
    /**
     * Accepts an element
     *
     * @param   io.collections.IOElement element
     * @return  bool
     */
    public function accept($element);
  
  }
?>
