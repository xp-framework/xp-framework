<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'de.thekid.util';
  
  /**
   * Object comparator
   *
   * @purpose  Comparator implementation
   */
  class de·thekid·util·ObjectComparator extends Object {
  
    /**
     * Compare two objects
     *
     * @access  public
     * @param   mixed a
     * @param   mixed b
     * @return  bool
     */
    function compare($a, $b) { 
      return $a->equals($b);
    }
  
  } implements('de·thekid·util·ObjectComparator', 'de.thekid.util.Comparator');
?>
