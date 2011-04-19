<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.collections.HashImplementation');

  /**
   * MD5 implementation that uses hexdec() on md5() to calculate the
   * numeric value instead of relying on addition taking care of this.
   *
   * Bug this works around:
   * <pre>
   *   $ php -r '$o= 0; $o+= "0x12195d4e54299a3cc1bde564c5de04b6"; var_dump($o);'
   *
   *   // 5.2.0 : int(0)
   *   // 5.2.10: float(2.4057803815529E+37)
   * </pre>
   *
   * @see      php://md5
   * @see      php://hexdec
   * @see      xp://util.collections.HashProvider
   * @purpose  Hashing
   */
  class MD5HexHashImplementation extends Object implements HashImplementation {

    /**
     * Retrieve hash code for a given string
     *
     * @param   string str
     * @return  int hashcode
     */
    public function hashOf($str) {
      return hexdec(md5($str));
    }
  } 
?>
