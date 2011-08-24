<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('security.crypto.CryptImpl');

  /**
   * MD5 Crypt implementation
   *
   * Based on the implementation found in FreeBSD 2.2.[56]-RELEASE, which
   * contains the following license:
   * <pre>
   *   "THE BEER-WARE LICENSE" (Revision 42):
   *   <phk@login.dknet.dk> wrote this file.  As long as you retain this notice you
   *   can do whatever you want with this stuff. If we meet some day, and you think
   *   this stuff is worth it, you can buy me a beer in return.   Poul-Henning Kamp
   * </pre>
   *
   * @see   php://md5
   * @see   php://pack
   * @see   xp://security.crypto.UnixCrypt
   * @see   https://bugs.php.net/bug.php?id=55439
   */
  class MD5CryptImpl extends security·crypto·CryptImpl {
    const MAGIC = '$1$';
    
    /**
     * Converts an integer to a base64 string
     *
     * @param   int value
     * @param   int length
     * @return  string
     */
    protected function to64($value, $length) {
      static $itoa= './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

      $r= '';
      while ($length--) {
        $r.= $itoa{$value & 0x3F};
        $value >>= 6;
      }
      return $r;
    }
    
    /**
     * Crypt a given plain-text string
     *
     * @param   string plain
     * @param   string salt salt, optionally including magic '$1$'
     * @return  string
     */
    public function crypt($plain, $salt) {
    
      // Take care of situation when magic is present in string
      if (0 === strpos($salt, self::MAGIC)) {
        $salt= substr($salt, strlen(self::MAGIC));
      }
      
      // Salt can have up to 8 characters, and goes max. until '$'
      $salt= substr($salt, 0, min(8, strpos($salt.'$', '$')));
      
      // Initial
      $ctx= $plain.self::MAGIC.$salt;
      $final= pack('H*', md5($plain.$salt.$plain));
      $l= strlen($plain);
      
      // Initial transformation
      for ($i= $l; $i > 0; $i-= 16) {
        $ctx.= substr($final, 0, $i > 16 ? 16 : $i);
      }
      
      // Memset
      for ($i= $l; $i; $i >>= 1) {
        $ctx.= $i & 1 ? "\0" : $plain{0};
      }
      
      // Slow down
      for ($final= pack('H*', md5($ctx)), $i= 0; $i < 1000; $i++) {
        $ctx1= $i & 1 ? $plain : substr($final, 0, 16);
        $i % 3 && $ctx1.= $salt;
        $i % 7 && $ctx1.= $plain;
        $ctx1.= $i & 1 ? substr($final, 0, 16) : $plain;
        $final= pack('H*', md5($ctx1));
      }
      
      // Final transformation
      return (
        self::MAGIC.$salt.'$'.
        $this->to64((ord($final{0}) << 16) | (ord($final{6}) << 8) | ord($final{12}), 4).
        $this->to64((ord($final{1}) << 16) | (ord($final{7}) << 8) | ord($final{13}), 4).
        $this->to64((ord($final{2}) << 16) | (ord($final{8}) << 8) | ord($final{14}), 4).
        $this->to64((ord($final{3}) << 16) | (ord($final{9}) << 8) | ord($final{15}), 4).
        $this->to64((ord($final{4}) << 16) | (ord($final{10}) << 8) | ord($final{5}), 4).
        $this->to64(ord($final{11}), 2)
      );
    }

    /**
     * Creates a string representation of this crypt implementation
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName();
    }
  }
?>
