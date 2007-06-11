<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('security.crypto.CryptoException');

  /**
   * Cryptographic Key base class.
   *
   * @ext      openssl
   * @see      http://openssl.org
   * @purpose  Crypto key base
   */
  class CryptoKey extends Object {
    public
      $_hdl = NULL;

    /**
     * Constructor
     *
     * @param   resource handle
     */
    public function __construct($handle) {
      $this->_hdl= $handle;
    }
    
    /**
     * Retrieves the handle for the key.
     *
     * @return  resource
     */
    public function getHandle() {
      return $this->_hdl;
    }    
    
    /**
     * Create a key from its string representation
     *
     * @param   string string
     * @return  security.crypto.CryptoKey
     */
    public static function fromString($string) { }
    
    /**
     * Encrypt data using this key
     *
     * @param   string data
     * @return  string
     */
    public function encrypt($data) { }
    
    /**
     * Decrypt data using this key
     *
     * @param   string data
     * @return  string 
     */
    public function decrypt($data) { }    
      
  }
?>
