<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'de.thekid.util';
  
  /**
   * Comparator interface
   *
   * @purpose  Interface
   */
  interface de·thekid·util·Comparator {
  
    /**
     * Compare two objects
     *
     * @param   mixed a
     * @param   mixed b
     * @return  bool
     */
    public function compare($a, $b);
  
  }
?>
