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
  interface HashImplementation {

    /**
     * Retrieve hash code for a given string
     *
     * @param   string str
     * @return  int hashcode
     */
    public function hashOf($str);

  }
?>
