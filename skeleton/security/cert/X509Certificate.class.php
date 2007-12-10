<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'security.cert.Certificate',
    'security.Principal',
    'security.crypto.PublicKey',
    'security.OpenSslUtil',
    'util.Date'
  );

  /**
   * X.509 certificate
   *
   * <code>
   *   uses('security.cert.X509Certificate');
   *   
   *   $x509= X509Certificate::fromString(<<<EOC
   * -----BEGIN CERTIFICATE-----
   * [...]
   * -----END CERTIFICATE-----
   * EOC
   *  );
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
   *     $x509->getSubjectDN()->getName(),
   *     $x509->getIssuerDN()->getName(),
   *     $x509->getSerialNumber(),
   *     $x509->getVersion(),
   *     $x509->getHash(),
   *     $x509->getNotBefore()->toString(),
   *     $x509->getNotAfter()->toString(),
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
    public
      $_res=  NULL,
      $_info= array();
    
    /**
     * Constructor
     *
     * @param   array _info
     * @param   resource _res
     */
    public function __construct($_info, $_res) {
      $this->_info= $_info;
      $this->_res= $_res;
    }
    
    /**
     * Checks validity
     *
     * @param   util.Date date default NULL (date to check against, defaulting to now)
     * @return  bool TRUE if this certificate is valid for the given date
     */
    public function checkValidity($date= NULL) {
      if (NULL === $date) $date= new Date();
      return (
        ($date->getTime() >= $this->_info['validFrom_time_t']) ||
        ($date->getTime() <= $this->_info['validTo_time_t'])
      );
    }
    
    /**
     * Gets the notBefore date from the validity period of the certificate.
     *
     * @return  util.Date
     */
    public function getNotBefore() {
      return new Date($this->_info['validFrom_time_t']);
    }
    
    /**
     * Gets the notAfter date from the validity period of the certificate.
     *
     * @return  util.Date
     */
    public function getNotAfter() {
      return new Date($this->_info['validTo_time_t']);
    }
    
    /**
     * Gets the issuer DN (distinguished name)
     *
     * @return  security.Principal
     */
    public function getIssuerDN() {
      if (version_compare(phpversion(), '5.2.2', '<')) {
        return new Principal($this->_info['issuer']);
      } else {
        return new Principal(array_map('utf8_decode', $this->_info['issuer']));
      }
    }
    
    /**
     * Gets the subject DN (distinguished name)
     *
     * @return  security.Principal
     */
    public function getSubjectDN() {
      if (version_compare(phpversion(), '5.2.2', '<')) {
        return new Principal($this->_info['subject']);
      } else {
        return new Principal(array_map('utf8_decode', $this->_info['subject']));
      }
    }
    
    /**
     * Retrieve certificate's version
     *
     * @return  int version
     */
    public function getVersion() {
      return $this->_info['version'];
    }

    /**
     * Retrieve certificate's serial number
     *
     * @return  int serial number
     */
    public function getSerialNumber() {
      return $this->_info['serialNumber'];
    }
    
    /**
     * Get certificate'shash
     *
     * @return  string hash
     */
    public function getHash() {
      return $this->_info['hash'];
    }
    
    /**
     * Gets a boolean array representing bits of the KeyUsage extension
     *
     * @return  array<string, bool>
     */
    public function getKeyUsage() {
      $usage= array();
      foreach ($this->_info['purposes'] as $v) {
        $usage[$v[2]]= $v[1];
      }
      return $usage;
    }

    /**
     * Export this certificate
     *
     * @return  string cert
     * @throws  security.cert.CertificateException
     */
    public function export() {
      if (FALSE === openssl_x509_export($this->_res, $out)) {
        throw new CertificateException(
          'Could not export certificate', OpenSslUtil::getErrors()
        );
      }
      
      return $out;
    }
    
    /**
     * Create a X.509 Certificate from a string
     *
     * @param   string str
     * @return  security.cert.X509Certificate
     * @throws  security.cert.CertificateException
     */
    public static function fromString($str) {
      if (!is_resource($_res= openssl_x509_read($str))) {
        throw new CertificateException(
          'Could not read certificate', OpenSslUtil::getErrors()
        );
      }
      if (!is_array($_info= openssl_x509_parse($_res, TRUE))) {
        throw new CertificateException(
          'Cannot parse certificate information', OpenSslUtil::getErrors()
        );
      }
      
      return new X509Certificate($_info, $_res);
    }
    
    /**
     * Check whether the given private key corresponds
     * to this certificate.
     *
     * @param   security.crypto.PrivateKey privatekey
     * @return  bool
     */
    public function checkPrivateKey($privatekey) {
      return openssl_x509_check_private_key($this->_res, $privatekey->getHandle());
    }
    
    /**
     * Retrieve the public key associated with this
     * certificate
     *
     * @return  security.crypto.PublicKey
     */
    public function getPublicKey() {
      return PublicKey::fromString($this->export());
    }
  
    /**
     * Destructor
     *
     */
    public function __destruct() {
      if (is_resource($this->_res)) openssl_x509_free($this->_res);
    }  
  }
?>
