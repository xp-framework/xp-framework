<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'security.cert.Certificate', 
    'security.Principal', 
    'security.OpenSslUtil',
    'util.Date'
  );

  /**
   * X.509 certificate
   *
   * <code>
   *   uses('security.cert.X509Certificate');
   *   
   *   try(); {
   *     $x509= &X509Certificate::fromString(<<<EOC
   * -----BEGIN CERTIFICATE-----
   * [...]
   * -----END CERTIFICATE-----
   * EOC
   * );
   *   } if (catch('CertificateException', $e)) {
   *     $e->printStackTrace();
   *     exit();
   *   }
   *   
   *   $subject= &$x509->getSubjectDN();
   *   $issuer= &$x509->getIssuerDN();
   *   $notBefore= &$x509->getNotBefore();
   *   $notAfter= &$x509->getNotAfter();
   *   
   *   printf(<<<EOP
   * Certificate information
   * ------------------------------------------------------------------------
   * Subject     %s
   * Issuer      %s
   * 
   * Serial#     %d
   * Version#    %d
   * Hash        %s
   * 
   * Valid from  %s
   * Valid until %s
   * Valid       %s
   * 
   * Purposes    %s
   * ------------------------------------------------------------------------
   * 
   * EOP
   *     ,
   *     $subject->getName(),
   *     $issuer->getName(),
   *     $x509->getSerialNumber(),
   *     $x509->getVersion(),
   *     $x509->getHash(),
   *     $notBefore->toString(),
   *     $notAfter->toString(),
   *     $x509->checkValidity() ? 'yes' : 'no',
   *     var_export($x509->getKeyUsage(), 1)
   *   );
   * </code>
   *
   * @ext      openssl
   * @see      rfc://2459 Internet X.509 Public Key Infrastructure Certificate and CRL Profile
   * @test     xp://net.xp_framework.unittest.security.CertificateTest
   * @purpose  Represent X509 certificate
   */
  class X509Certificate extends Certificate {
    var
      $_res=  NULL,
      $_info= array();
    
    /**
     * Constructor
     *
     * @access  private
     * @param   array _info
     * @param   resource _res
     */
    function __construct($_info, $_res) {
      $this->_info= $_info;
      $this->_res= $_res;
    }
    
    /**
     * Checks validity
     *
     * @access  public
     * @param   util.Date date default NULL (date to check against, defaulting to now)
     * @return  bool TRUE if this certificate is valid for the given date
     */
    function checkValidity($date= NULL) {
      if (NULL === $date) $date= &new Date(time());
      return (
        ($date->getTime() >= $this->_info['validFrom_time_t']) ||
        ($date->getTime() <= $this->_info['validTo_time_t'])
      );
    }
    
    /**
     * Gets the notBefore date from the validity period of the certificate.
     *
     * @access  public
     * @return  &util.Date
     */
    function &getNotBefore() {
      return new Date($this->_info['validFrom_time_t']);
    }
    
    /**
     * Gets the notAfter date from the validity period of the certificate.
     *
     * @access  public
     * @return  &util.Date
     */
    function &getNotAfter() {
      return new Date($this->_info['validTo_time_t']);
    }
    
    /**
     * Gets the issuer DN (distinguished name)
     *
     * @access  public
     * @return  &security.Principal
     */
    function &getIssuerDN() {
      return new Principal($this->_info['issuer']);
    }
    
    /**
     * Gets the subject DN (distinguished name)
     *
     * @access  public
     * @return  &security.Principal
     */
    function &getSubjectDN() {
      return new Principal($this->_info['subject']);
    }
    
    /**
     * Retrieve certificate's version
     *
     * @access  public
     * @return  int version
     */
    function getVersion() {
      return $this->_info['version'];
    }

    /**
     * Retrieve certificate's serial number
     *
     * @access  public
     * @return  int serial number
     */
    function getSerialNumber() {
      return $this->_info['serialNumber'];
    }
    
    /**
     * Get certificate'shash
     *
     * @access  public
     * @return  string hash
     */
    function getHash() {
      return $this->_info['hash'];
    }
    
    /**
     * Gets a boolean array representing bits of the KeyUsage extension
     *
     * @access  public
     * @return  object
     */
    function getKeyUsage() {
      $usage= &new stdClass();
      foreach ($this->_info['purposes'] as $v) {
        $usage->{$v[2]}= $v[1];
      }
      return $usage;
    }

    /**
     * Export this certificate
     *
     * @access  public
     * @return  string cert
     * @throws  security.cert.CertificateException
     */
    function export() {
      if (FALSE === openssl_x509_export($this->_res, $out)) {
        return throw(new CertificateException(
          'Could not export certificate', OpenSslUtil::getErrors()
        ));
      }
      
      return $out;
    }
    
    /**
     * Create a X.509 Certificate from a string
     *
     * @model   static
     * @access  public
     * @param   string str
     * @return  &security.cert.X509Certificate
     * @throws  security.cert.CertificateException
     */
    function &fromString($str) {
      if (!is_resource($_res= openssl_x509_read($str))) {
        return throw(new CertificateException(
          'Could not read certificate', OpenSslUtil::getErrors()
        ));
      }
      if (!is_array($_info= openssl_x509_parse($_res, TRUE))) {
        return throw(new CertificateException(
          'Cannot parse certificate information', OpenSslUtil::getErrors()
        ));
      }
      
      return new X509Certificate($_info, $_res);
    }
    
    /**
     * Check whether the given private key corresponds
     * to this certificate.
     *
     * @access  public
     * @param   &security.crypto.PrivateKey privatekey
     * @return  bool
     */
    function checkPrivateKey(&$privatekey) {
      return openssl_x509_check_private_key($this->_res, $privatekey->getHandle());
    }
    
    /**
     * Retrieve the public key associated with this
     * certificate
     *
     * @access  public
     * @return  &security.crypto.PublicKey
     */
    function &getPublicKey() {
      return PublicKey::fromString($this->export());
    }
  
    /**
     * Destructor
     *
     * @access  public
     */
    function __destruct() {
      if (is_resource($this->_res)) openssl_x509_free($this->_res);
    }  
  }
?>
