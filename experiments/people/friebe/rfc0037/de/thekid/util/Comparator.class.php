<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'de.thekid.util';
  
  uses('de.thekid.List');

  /**
   * Comparator interface
   *
   * @purpose  Interface
   */
  class de·thekid·util·Comparator extends Interface {
  
    /**
     * Compare two objects
     *
     * @access  public
     * @param   mixed a
     * @param   mixed b
     * @return  bool
     */
    function compare($a, $b) { }
  
  }
?>
