<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('security.cert.X509Certificate');

  /**
   * Certificate signing requests
   *
   * @ext      openssl
   * @purpose  Represent a CSR
   */
  class CSR extends Object {
  
    /**
     * Constructor
     *
     * @access  public
     * @param   &security.Principal principal
     * @param   &security.KeyPair keypair
     */
    function __construct(&$principal, &$keypair) {
      $this->_res= openssl_csr_new(array(
        'countryName'               => $principal->getCountryName(),
        'stateOrProvinceName'       => $principal->getStateOrProvinceName(),
        'localityName'              => $principal->getLocalityName(),
        'organizationName'          => $principal->getOrganizationName(),
        'organizationalUnitName'    => $principal->getOrganizationalUnitName(),
        'commonName'                => $principal->getCommonName(),
        'emailAddress'              => $principal->getEmailAddress()
      ), $keypair->_res);
      parent::__construct();
    }
    
    /**
     * Export this CSR
     *
     * @access  public
     * @return  string CSR
     */
    function export() {
      if (FALSE === openssl_csr_export($this->_res, $out)) {
        trigger_error(implode("\n  @", OpenSslUtil::getErrors()), E_USER_NOTICE);
        return throw(new Exception('Could not export CSR'));
      }
      
      return $out;
    }
    
    /**
     * Sign this CSR
     *
     * @access  public
     * @param   &security.KeyPair keypair
     * @param   int days default 365
     * @param   mixed cacert default NULL
     * @return  &security.cert.X509Certificate
     */
    function &sign(&$keypair, $days= 365, $cacert= NULL) {
      if (FALSE === ($x509= openssl_csr_sign($this->_res, $cacert, $keypair->_res, 365))) {
        trigger_error(implode("\n  @", OpenSslUtil::getErrors()), E_USER_NOTICE);
        return throw(new CertificateException('Cannot sign certificate'));
      }      
      if (FALSE === openssl_x509_export($x509, $str)) {
        trigger_error(implode("\n  @", OpenSslUtil::getErrors()), E_USER_NOTICE);
        return throw(new CertificateException('Cannot export certificate'));
      }
      
      return X509Certificate::fromString($str);
    }
  }
?>
