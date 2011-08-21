<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'security.crypto';

  uses('security.crypto.CryptoException');
 
  /**
   * Crypt implementation
   *
   * @see   php://crypt
   * @see   xp://security.crypto.UnixCrypt
   */
  abstract class security·crypto·CryptImpl extends Object {
    
    /**
     * Crypt a given plain-text string
     *
     * @param   string plain
     * @param   string salt
     * @return  string
     * @throws  security.crypto.CryptoException
     */
    public abstract function crypt($plain, $salt);
    
    /**
     * Check if an entered string matches the crypt
     *
     * @param   string encrypted
     * @param   string entered
     * @return  bool
     */
    public function matches($encrypted, $entered) {
      return ($encrypted === $this->crypt($entered, $encrypted));
    }
  }
?>
