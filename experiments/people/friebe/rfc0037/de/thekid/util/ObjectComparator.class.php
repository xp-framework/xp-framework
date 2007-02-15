<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'de.thekid.util';
  
  uses('de.thekid.util.Comparator');
  
  /**
   * Object comparator
   *
   * @purpose  Comparator implementation
   */
  class de·thekid·util·ObjectComparator extends Object implements de·thekid·util·Comparator {
  
    /**
     * Compare two objects
     *
     * @param   mixed a
     * @param   mixed b
     * @return  bool
     */
    public function compare($a, $b) { 
      return $a->equals($b);
    }
  
  }
?>
