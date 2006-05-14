<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Hash implementation
   *
   * @see      xp://util.collections.HashProvider
   * @purpose  Interface
   */
  class HashImplementation extends Interface {

    /**
     * Retrieve hash code for a given string
     *
     * @access  public
     * @param   string str
     * @return  int hashcode
     */
    function hashOf($str) { }

  }
?>
