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
     * @param   array _info information from openssl_x509_parse() or NULL to trigger auto-parsing
     * @param   resource _res resource handle of x.509 certificate
     */
    public function __construct($_info, $_res) {
      $this->_info= $_info;
      $this->_res= $_res;

      if (NULL === $_info) {
        if (!is_array($this->_info= openssl_x509_parse($_res, TRUE))) {
          throw new CertificateException(
            'Cannot parse certificate information', OpenSslUtil::getErrors()
          );
        }
      }
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
     * Callback for decoding
     *
     * @param   string value
     * @return  string
     */
    protected static function decode($value) {
      return iconv('utf-8', xp::ENCODING, $value);
    }
    
    /**
     * Gets the issuer DN (distinguished name)
     *
     * @return  security.Principal
     */
    public function getIssuerDN() {
      return new Principal(array_map(array('self', 'decode'), $this->_info['issuer']));
    }
    
    /**
     * Gets the subject DN (distinguished name)
     *
     * @return  security.Principal
     */
    public function getSubjectDN() {
      return new Principal(array_map(array('self', 'decode'), $this->_info['subject']));
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
     * @return  [:bool]
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

      return new X509Certificate(NULL, $_res);
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

    /**
     * Retrieve string representation
     *
     * @return  string
     */
    public function toString() {
      $s= $this->getClassName().'@(hash= '.$this->getHash().'; version= '.$this->getVersion().") {\n";
      $s.= '  [     Serial ] '.$this->getSerialNumber()."\n";
      $s.= '  [    Subject ] '.xp::stringOf($this->getSubjectDN())."\n";
      $s.= '  [     Issuer ] '.xp::stringOf($this->getIssuerDN())."\n";
      $s.= '  [   Purposes ] ';

      foreach ($this->getKeyUsage() as $type => $u) {
        if ($u) $p.= $type.', ';
      }

      $s.= rtrim($p, ', ')."\n";
      $s.= '  [ Not before ] '.xp::stringOf($this->getNotBefore())."\n";
      $s.= '  [  Not after ] '.xp::stringOf($this->getNotAfter())."\n";

      return $s."}\n";
    }
  }
?>
