<?php
/* This class is part of the XP framework
 *
 * $Id: DJBX33AHashImplementation.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace util::collections;

  ::uses('util.collections.HashImplementation');

  /**
   * DJBX33A (Daniel J. Bernstein, Times 33 with Addition)
   *
   * This is Daniel J. Bernstein's popular `times 33' hash function as
   * posted by him years ago on comp.lang.c. It basically uses a function
   * like ``hash(i) = hash(i-1) * 33 + str[i]''. This is one of the best
   * known hash functions for strings. Because it is both computed very
   * fast and distributes very well.
   *
   * The magic of number 33, i.e. why it works better than many other
   * constants, prime or not, has never been adequately explained by
   * anyone. So I try an explanation: if one experimentally tests all
   * multipliers between 1 and 256 (as RSE did now) one detects that even
   * numbers are not useable at all. The remaining 128 odd numbers
   * (except for the number 1) work more or less all equally well. They
   * all distribute in an acceptable way and this way fill a hash table
   * with an average percent of approx. 86%. 
   *
   * If one compares the Chi^2 values of the variants, the number 33 not
   * even has the best value. But the number 33 and a few other equally
   * good numbers like 17, 31, 63, 127 and 129 have nevertheless a great
   * advantage to the remaining numbers in the large set of possible
   * multipliers: their multiply operation can be replaced by a faster
   * operation based on just one shift plus either a single addition
   * or subtraction operation. And because a hash function has to both
   * distribute good _and_ has to be very fast to compute, those few
   * numbers should be preferred and seems to be the reason why Daniel J.
   * Bernstein also preferred it.
   *
   * -- Ralf S. Engelschall
   *
   * @see      xp://util.collections.HashProvider
   * @purpose  Hashing
   */
  class DJBX33AHashImplementation extends lang::Object implements HashImplementation {

    /**
     * Retrieve hash code for a given string
     *
     * @param   string str
     * @return  int hashcode
     */
    public function hashOf($str) {
      $hash= 5381;
      $offset= 0;
      for ($len= strlen($str); $len >= 8; $len-= 8) {
        $hash= (($hash << 5) + $hash) + ord($str{$offset++});
        $hash= (($hash << 5) + $hash) + ord($str{$offset++});
        $hash= (($hash << 5) + $hash) + ord($str{$offset++});
        $hash= (($hash << 5) + $hash) + ord($str{$offset++});
        $hash= (($hash << 5) + $hash) + ord($str{$offset++});
        $hash= (($hash << 5) + $hash) + ord($str{$offset++});
        $hash= (($hash << 5) + $hash) + ord($str{$offset++});
        $hash= (($hash << 5) + $hash) + ord($str{$offset++});
      }

      switch ($len) {
        case 7: $hash= (($hash << 5) + $hash) + ord($str{$offset++});
        case 6: $hash= (($hash << 5) + $hash) + ord($str{$offset++});
        case 5: $hash= (($hash << 5) + $hash) + ord($str{$offset++});
        case 4: $hash= (($hash << 5) + $hash) + ord($str{$offset++});
        case 3: $hash= (($hash << 5) + $hash) + ord($str{$offset++});
        case 2: $hash= (($hash << 5) + $hash) + ord($str{$offset++});
        case 1: $hash= (($hash << 5) + $hash) + ord($str{$offset++});
      }
      return $hash;
    }

  } 
?>
