<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.collections.HashImplementation');

  /**
   * MD5
   *
   * @see   php://md5
   * @see   xp://util.collections.HashProvider
   * @test  xp://net.xp_framework.unittest.util.collections.MD5HashImplementationTest:
   */
  class MD5HashImplementation extends Object implements HashImplementation {

    /**
     * Retrieve hash code for a given string
     *
     * @param   string str
     * @return  int hashcode
     */
    public function hashOf($str) {
      return '0x'.md5($str);
    }
  } 
?>
