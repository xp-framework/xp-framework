<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_framework.unittest.core.extensions.ArrayListExtensions');

  /**
   * Demo class that uses the ArrayList extension methods
   *
   * @see   xp://net.xp_framework.unittest.core.extensions.ArrayListExtensions
   */
  class ArrayListDemo extends Object {
    
    /**
     * Main method
     *
     * @param   string[] args
     */
    public static function main($args) {
      $sorted= ArrayList::newInstance($args)->sorted();
      Console::writeLine('create(new ArrayList(array(', implode(', ', $args), ')))->sorted()= ', $sorted);
    }
  }
?>
