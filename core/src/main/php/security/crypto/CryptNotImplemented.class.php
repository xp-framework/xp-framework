<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('security.crypto.CryptImpl');

  /**
   * Implementation which always raises a "Not Implemented" exception
   *
   * @see   xp://security.crypto.UnixCrypt
   */
  class CryptNotImplemented extends security·crypto·CryptImpl {
    protected $method= '';
    
    /**
     * Not implemented
     *
     * @param   string method
     */
    public function __construct($method) {
      $this->method= $method;
    }
  
    /**
     * Crypt a given plain-text string
     *
     * @param   string plain
     * @param   string salt
     * @return  string
     */
    public function crypt($plain, $salt) {
      throw new CryptoException('Method '.$this->method.' not implemented');
    }

    /**
     * Creates a string representation of this crypt implementation
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'<'.$this->method.'>';
    }
  }
?>
