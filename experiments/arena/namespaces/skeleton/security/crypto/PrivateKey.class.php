<?php
/* This class is part of the XP framework
 *
 * $Id: PrivateKey.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace security::crypto;

  uses('security.crypto.CryptoKey', 'security.OpenSslUtil');

  /**
   * Private key
   *
   * @purpose  Private key
   */
  class PrivateKey extends CryptoKey {

    /**
     * Create private key from its string representation
     *
     * @param   string str
     * @param   string passphrase default NULL
     * @return  security.crypto.PrivateKey
     * @throws  security.crypto.CryptoException if the operation fails
     */
    public static function fromString($str, $passphrase= ) {
      if (!is_resource($_hdl= openssl_pkey_get_private($str, $passphrase))) {
        throw(new CryptoException(
          'Could not read private key', security::OpenSslUtil::getErrors()
        ));
      }
    
      $pk= new ($_hdl);
      return $pk;
    }
    
    /**
     * Signs the data using this private key
     *
     * @param   string data
     * @return  string
     * @throws  security.crypto.CryptoException if the operation fails
     */
    public function sign($data) {
      if (FALSE === openssl_sign($data, $signature, $this->_hdl)) {
        throw(new CryptoException(
          'Could not sign data', security::OpenSslUtil::getErrors()
        ));
      }
      
      return $signature;
    }
    
    /**
     * Encrypt data using this public key. Data will be decryptable
     * only with the matching private key.
     *
     * This method can only encrypt short data (= shorter than the key,
     * see the PHP manual). To encrypt larger values, use the seal()
     * method.
     *
     * @see     php://openssl_private_encrypt
     * @param   string data
     * @return  string
     * @throws  security.crypto.CryptoException if the operation fails
     */
    public function encrypt($data) {
      if (FALSE === openssl_private_encrypt($data, $crypted, $this->_hdl)) {
        throw(new CryptoException(
          'Could not decrypt data', security::OpenSslUtil::getErrors()
        ));
      }
      
      return $crypted;
    }    
    
    /**
     * Decrypt data using this private key. Only data encrypted with
     * the public key matching this key will be decryptable.
     *
     * @param   string data
     * @return  string
     * @throws  security.crypto.CryptoException if the operation fails
     */
    public function decrypt($data) {
      if (FALSE === openssl_private_decrypt($data, $decrypted, $this->_hdl)) {
        throw(new CryptoException(
          'Could not decrypt data', security::OpenSslUtil::getErrors()
        ));
      }
      
      return $decrypted;
    }
    
    /**
     * Export this key into its string representation
     *
     * @param   string passphrase default NULL
     * @return  string
     * @throws  security.crypto.CryptoException if the operation fails
     */
    public function export($passphrase= ) {
      if (FALSE === openssl_pkey_export($this->_hdl, $out, $passphrase)) {
        throw(new CryptoException(
          'Could not export private key', security::OpenSslUtil::getErrors()
        ));
      }
      
      return $out;
    }
    
    /**
     * Unseal data sealed with the public key matching this key. This method
     * also needs the hash-key created by the seal() method.
     *
     * @param   string data
     * @param   string key
     * @return  string
     * @throws  security.crypto.CryptoException if the operation fails
     */
    public function unseal($data, $key) {
      if (FALSE === openssl_open($data, $unsealed, $key, $this->_hdl)) {
        throw(new CryptoException(
          'Could not export private key', security::OpenSslUtil::getErrors()
        ));
      }
      
      return $unseal;
    }
  }

?>
