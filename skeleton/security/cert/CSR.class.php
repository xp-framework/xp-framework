<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('security.cert.X509Certificate', 'security.KeyPair');

  /**
   * Certificate signing requests
   *
   * Example [Creating a self-signed-certificate]:
   * <code>
   *   uses('security.cert.CSR');
   * 
   *   try {
   *     if ($keypair= KeyPair::generate('md5', OPENSSL_KEYTYPE_RSA)) {
   *       $csr= new CSR(new Principal(array(
   *         'C'     => 'DE',
   *         'ST'    => 'Baden-Württemberg',
   *         'L'     => 'Karlsruhe',
   *         'O'     => 'XP',
   *         'OU'    => 'XP Team',
   *         'CN'    => 'Timm Friebe',
   *         'EMAIL' => 'xp@php3.de'
   *       )), $keypair);
   *       $cert= $csr->sign($keypair);
   *     }
   *   } catch(XPException $e) {
   *     $e->printStackTrace();
   *     exit();
   *   }
   *   
   *   var_dump(
   *     $keypair,
   *     $keypair->export('password'),
   *     $csr,
   *     $csr->export(),
   *     $cert,
   *     $cert->export()
   *   );
   * </code>
   *
   * @ext      openssl
   * @purpose  Represent a CSR
   */
  class CSR extends Object {
  
    /**
     * Constructor
     *
     * @param   security.Principal principal
     * @param   security.KeyPair keypair
     */
    public function __construct($principal, $keypair) {
      $this->_res= openssl_csr_new(array(
        'countryName'               => $principal->getCountryName(),
        'stateOrProvinceName'       => $principal->getStateOrProvinceName(),
        'localityName'              => $principal->getLocalityName(),
        'organizationName'          => $principal->getOrganizationName(),
        'organizationalUnitName'    => $principal->getOrganizationalUnitName(),
        'commonName'                => $principal->getCommonName(),
        'emailAddress'              => $principal->getEmailAddress()
      ), $keypair->_res);
      
    }
    
    /**
     * Export this CSR
     *
     * @return  string CSR
     */
    public function export() {
      if (FALSE === openssl_csr_export($this->_res, $out)) {
        trigger_error(implode("\n  @", OpenSslUtil::getErrors()), E_USER_NOTICE);
        throw new XPException('Could not export CSR');
      }
      
      return $out;
    }
    
    /**
     * Sign this CSR
     *
     * @param   security.KeyPair keypair
     * @param   int days default 365
     * @param   var cacert default NULL
     * @return  security.cert.X509Certificate
     */
    public function sign($keypair, $days= 365, $cacert= NULL) {
      if (FALSE === ($x509= openssl_csr_sign($this->_res, $cacert, $keypair->_res, $days))) {
        trigger_error(implode("\n  @", OpenSslUtil::getErrors()), E_USER_NOTICE);
        throw new CertificateException('Cannot sign certificate');
      }      
      if (FALSE === openssl_x509_export($x509, $str)) {
        trigger_error(implode("\n  @", OpenSslUtil::getErrors()), E_USER_NOTICE);
        throw new CertificateException('Cannot export certificate');
      }
      
      return X509Certificate::fromString($str);
    }
  }
?>
