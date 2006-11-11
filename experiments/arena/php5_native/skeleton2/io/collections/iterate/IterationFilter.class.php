<?php
/* This class is part of the XP framework
 *
 * $Id: IterationFilter.class.php 7944 2006-09-21 11:32:15Z friebe $ 
 */

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
     * @access  public
     * @param   &io.collections.IOElement element
     * @return  bool
     */
    public function accept(&$element);
  
  }
?>
