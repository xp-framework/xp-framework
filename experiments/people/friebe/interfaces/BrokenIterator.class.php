<?php
/* This class is part of the XP framework's people experiments
 *
 * $Id$
 */

  /**
   * Broken iterator
   *
   * @purpose  Iterator
   */
  class BrokenIterator extends Object {
  
    /**
     * Get next element
     *
     * @access  public
     * @return  &mixed
     */
    function &next() { }

  } implements('Iterator');
?>
