<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('security.crypto.CryptoKey', 'security.OpenSslUtil');

  /**
   * Public Key
   *
   * @purpose  Public Key
   */
  class PublicKey extends CryptoKey {
      
    /**
     * Create from certificate string representation.
     *
     * @param   string string
     * @return  security.crypto.PublicKey
     * @throws  security.crypto.CryptoException if the operation fails
     */
    public static function fromString($string) {
      if (!is_resource($_hdl= openssl_pkey_get_public($string))) {
        throw new CryptoException(
          'Could not read public key', OpenSslUtil::getErrors()
        );
      }
      
      $pk= new PublicKey($_hdl);
      return $pk;
    }
    
    /**
     * Verify the given data with the signature. If the data and/or the
     * signature have been modified, or the signature has not been created
     * using the private key matching this one, verification will
     * fail.
     *
     * @param   string data
     * @param   string signature
     * @return  bool TRUE if data + signature are valid
     * @throws  security.crypto.CryptoException if the operation fails
     */
    public function verify($data, $signature) {
      if (-1 === ($res= openssl_verify($data, $signature, $this->_hdl))) {
        throw new CryptoException(
          'Error verifying signature', OpenSslUtil::getErrors()
        );
      }
      
      return (1 === $res);
    }
    
    /**
     * Encrypt data using this public key. Data will be decryptable
     * only with the matching private key.
     *
     * This method can only encrypt short data (= shorter than the key,
     * see the PHP manual). To encrypt larger values, use the seal()
     * method.
     *
     * @see     php://openssl_public_encrypt
     * @param   string data
     * @return  string
     * @throws  security.crypto.CryptoException if the operation fails
     */
    public function encrypt($data) {
      if (FALSE === openssl_public_encrypt($data, $out, $this->_hdl)) {
        throw new CryptoException(
          'Error encrypting data', OpenSslUtil::getErrors()
        );
      }
    
      return $out;
    }
    
    /**
     * Decrypt data using this public key. Only data encrypted with
     * the private key matching this key will be decryptable.
     *
     * @param   string data
     * @return  string
     * @throws  security.crypto.CryptoException if the operation fails
     */
    public function decrypt($data) {
      if (FALSE === openssl_public_decrypt($data, $decrypted, $this->_hdl)) {
        throw new CryptoException(
          'Could not decrypt data', OpenSslUtil::getErrors()
        );
      }
      
      return $decrypted;
    }
    
    /**
     * Seal data using this public key. This method returns two strings,
     * the first one being the encoded data, the second a key that has to
     * be passed to the recipient, too.
     *
     * @param   string data
     * @return  array<string,string>[1] first element is data, second is the key
     * @throws  security.crypto.CryptoException if the operation fails
     */
    public function seal($data) {
      if (FALSE === openssl_seal($data, $sealed, $keys, array($this->_hdl))) {
        throw new CryptoException(
          'Could not seal data', OpenSslUtil::getErrors()
        );
      }
      
      return array($sealed, $keys[0]);
    }
  }
?>
