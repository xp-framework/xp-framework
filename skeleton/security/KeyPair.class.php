<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'security.OpenSslUtil',
    'security.crypto.PublicKey',
    'security.crypto.PrivateKey'
  );

  /**
   * Key pair
   *
   * <code>
   *   uses('security.KeyPair');
   * 
   *   try(); {
   *     if ($keypair= &KeyPair::generate('md5', OPENSSL_KEYTYPE_RSA, 384)) {
   *       $export= $keypair->export('krowemarf-px');
   *     }
   *   } if (catch('Exception', $e)) {
   *     $e->printStackTrace();
   *     exit();
   *   }
   *   
   *   var_dump(
   *     $keypair,
   *     $export
   *   );
   * </code>
   *
   * @see      php://openssl_pkey_new
   * @ext      openssl
   * @purpose  purpose
   */
  class KeyPair extends Object {
  
    /**
     * Generates a new private and public key pair.
     *
     * Supported algorithms
     * <pre>
     * md2       MD2 Digest           
     * md5       MD5 Digest           
     * mdc2      MDC2 Digest          
     * rmd160    RMD-160 Digest       
     * sha       SHA Digest           
     * sha1      SHA-1 Digest         
     * </pre>
     *
     * @param   string algorithm default "md5"
     * @param   int type default OPENSSL_KEYTYPE_RSA
     * @param   int bits default 1024
     * @return  security.KeyPair
     */
    public function generate($algorithm= 'md5', $type= OPENSSL_KEYTYPE_RSA, $bits= 1024) {
      if (FALSE === ($res= openssl_pkey_new(array(
        'digest_alg'        => $algorithm,
        'private_key_type'  => $type,
        'private_key_bits'  => $bits
      )))) {
        trigger_error(implode("\n  @", OpenSslUtil::getErrors()), E_USER_NOTICE);
        throw(new XPException('Could not generate keypair'));
      }
      
      $k= new KeyPair();
      $k->_res= $res;
      return $k;
    }
    
    /**
     * Export this keypair
     *
     * @param   string passphrase default NULL
     * @return  string key
     */
    public function export($passphrase= NULL) {
      if (FALSE === openssl_pkey_export($this->_res, $out, $passphrase)) {
        trigger_error(implode("\n  @", OpenSslUtil::getErrors()), E_USER_NOTICE);
        throw(new XPException('Could not export key'));
      }
      
      return $out;
    }
    
    /**
     * Retrieves the private key associated with this keypair
     *
     * @return  security.crypto.PrivateKey
     */
    public function getPrivateKey() {
      return new PrivateKey(openssl_pkey_get_private($this->export(NULL)));
    }
  }
?>
