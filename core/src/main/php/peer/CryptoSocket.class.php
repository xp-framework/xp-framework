<?php
  uses(
    'peer.Socket',
    'security.cert.X509Certificate'
  );

  /**
   * Intermediate common class for all cryptographic socket classes such
   * as SSLSocket and TLSSocket.
   *
   */
  class CryptoSocket extends Socket {
    const CTX_WRP = 'ssl';      // stream context option key

    /**
     * Set verify peer
     *
     * @param   bool b
     */
    public function setVerifyPeer($b) {
      $this->setSocketOption(self::CTX_WRP, 'verify_peer', $b);
    }

    /**
     * Retrieve verify peer
     *
     * @return  bool
     */
    public function getVerifyPeer() {
      return $this->getSocketOption(self::CTX_WRP, 'verify_peer');
    }

    /**
     * Set allow self signed certificates
     *
     * @param   bool b
     */
    public function setAllowSelfSigned($b) {
      $this->setSocketOption(self::CTX_WRP, 'allow_self_signed', $b);
    }

    /**
     * Retrieve allow self signed certificates
     *
     * @return  bool
     */
    public function getAllowSelfSigned() {
      return $this->getSocketOption(self::CTX_WRP, 'allow_self_signed');
    }

    /**
     * Set CA file for peer verification
     *
     * @param   string f
     */
    public function setCAFile($f) {
      $this->setSocketOption(self::CTX_WRP, 'cafile', $f);
    }

    /**
     * Retrieve CA file for peer verification
     *
     * @return  string
     */
    public function getCAFile() {
      $this->getSocketOption(self::CTX_WRP, 'cafile');
    }

    /**
     * Set CA path for peer verification
     *
     * @param   string p
     */
    public function setCAPath($p) {
      $this->setSocketOption(self::CTX_WRP, 'capath', $p);
    }

    /**
     * Retrieve CA path for peer verification
     *
     * @return  string
     */
    public function getCAPath() {
      $this->setSocketOption(self::CTX_WRP, 'capath');
    }

    /**
     * Set capture peer certificate
     *
     * @param   bool b
     */
    public function setCapturePeerCertificate($b) {
      $this->setSocketOption(self::CTX_WRP, 'capture_peer_cert', $b);
    }

    /**
     * Retrieve capture peer certificate setting
     *
     * @return  bool
     */
    public function getCapturePeerCertificate() {
      return $this->getSocketOption(self::CTX_WRP, 'capture_peer_cert');
    }

    /**
     * Set capture peer certificate chain
     *
     * @param   bool b
     */
    public function setCapturePeerCertificateChain($b) {
      $this->setSocketOption(self::CTX_WRP, 'capture_peer_cert_chain', $b);
    }

    /**
     * Retrieve capture peer certificate chain setting
     *
     * @return  bool
     */
    public function getCapturePeerCertificateChain() {
      return $this->getSocketOption(self::CTX_WRP, 'capture_peer_cert_chain');
    }

    /**
     * Retrieve captured peer certificate
     *
     * @return  security.cert.X509Certificate
     * @throws  lang.IllegalStateException if capturing is disabled
     */
    public function getPeerCertificate() {
      if (!$this->getCapturePeerCertificate()) {
        throw new IllegalStateException('Cannot get peer\'s certificate, if capturing is disabled.');
      }

      return new X509Certificate(NULL, $this->getSocketOption(self::CTX_WRP, 'peer_certificate'));
    }

    /**
     * Retrieve captured peer certificate chain
     *
     * @return  security.cert.X509Certificate[]
     * @throws  lang.IllegalStateException if capturing is disabled
     */
    public function getPeerCertificateChain() {
      if (!$this->getCapturePeerCertificate()) {
        throw new IllegalStateException('Cannot get peer\'s certificate chain, if capturing is disabled.');
      }

      $chain= array();
      foreach ($this->getSocketOption(self::CTX_WRP, 'peer_certificate_chain') as $cert) {
        $chain[]= new X509Certificate(NULL, $cert);
      }

      return $chain;
    }
  }
?>