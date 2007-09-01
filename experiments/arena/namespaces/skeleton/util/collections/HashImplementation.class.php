<?php
/* This class is part of the XP framework
 *
 * $Id: HashImplementation.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace util::collections;

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
