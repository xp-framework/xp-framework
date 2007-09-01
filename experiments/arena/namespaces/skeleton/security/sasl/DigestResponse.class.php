<?php
/* This class is part of the XP framework
 *
 * $Id: DigestResponse.class.php 8974 2006-12-27 17:29:09Z friebe $ 
 */

  namespace security::sasl;

  uses('security.checksum.HMAC_MD5');

  /**
   * Digest challenge response
   *
   * @see      rfc://2831
   * @purpose  Challenge Response wrapper
   */
  class DigestResponse extends lang::Object {
    public
      $qop          = '',
      $user         = '',
      $pass         = '',
      $authzid      = NULL,
      $nonce        = '',
      $ncount       = 1,
      $digestUri    = '',
      $charset      = '';

    /**
     * Constructor
     *
     * @param   string realm
     * @param   string nonce
     * @param   string qop
     */
    public function __construct($realm, $nonce, $qop) {
      $this->realm= $realm;
      $this->nonce= $nonce;
      $this->qop= $qop;
    }

    /**
     * Helper method for encoding strings
     *
     * @param   string value
     * @return  string
     */    
    protected function _encode($value) {
      if (0 == strcasecmp('utf-8', $this->charset)) {
        return utf8_encode($value);
      }        
      return $value;
    }
    
    /**
     * Retrieve response string
     *
     * @return  string
     */
    public function getString() {
      $cnonce= base64_encode(bin2hex(security::checksum::HMAC_MD5::hash(microtime())));
      $ncount= sprintf('%08d', $this->ncount);
      
      $username_value= security::checksum::HMAC_MD5::hash(sprintf(
        '%s:%s:%s',
        $this->_encode($this->user),
        $this->_encode($this->realm),
        $this->_encode($this->pass)
      ));

      // If authzid is specified, then A1 is
      // 
      //    A1 = { H( { username-value, ":", realm-value, ":", passwd } ),
      //         ":", nonce-value, ":", cnonce-value, ":", authzid-value }
      // 
      // If authzid is not specified, then A1 is
      // 
      //    A1 = { H( { username-value, ":", realm-value, ":", passwd } ),
      //         ":", nonce-value, ":", cnonce-value }
      // 
      $a1= sprintf(
        '%s:%s:%s%s',
        $username_value,
        $this->nonce,
        $cnonce,
        $this->authzid ? ':'.$this->_encode($this->authzid) : ''
      );

      // If the "qop" directive's value is "auth", then A2 is:
      // 
      //     A2       = { "AUTHENTICATE:", digest-uri-value }
      // 
      //  If the "qop" value is "auth-int" or "auth-conf" then A2 is:
      // 
      //     A2       = { "AUTHENTICATE:", digest-uri-value,
      //              ":00000000000000000000000000000000" }
      switch ($this->qop) {
        case 'auth':
          $a2= 'AUTHENTICATE:'.$this->digestUri;
          break;
        
        case 'auth-int':
        case 'auth-conf':
          $a2= 'AUTHENTICATE:'.$this->digestUri.':00000000000000000000000000000000';
          break;
      }

      // response-value  =
      // 
      //     HEX( KD ( HEX(H(A1)),
      //             { nonce-value, ":" nc-value, ":",
      //               cnonce-value, ":", qop-value, ":", HEX(H(A2)) }))
      $response_value= bin2hex(security::checksum::HMAC_MD5::hash(sprintf(
        '%s:%s:%s:%s:%s:%s',
        bin2hex(security::checksum::HMAC_MD5::hash($a1)),
        $this->nonce,
        $ncount,
        $cnonce,
        $this->qop,
        bin2hex(security::checksum::HMAC_MD5::hash($a2))
      )));

      return sprintf(
        '%susername="%s",realm="%s",nonce="%s",nc=%s,'.
        'cnonce="%s",digest-uri="%s",response=%s,qop=%s%s',
        $this->charset ? 'charset='.$this->charset.',' : '',
        $this->_encode($this->user),
        $this->_encode($this->realm),
        $this->nonce,
        $ncount,
        $cnonce,
        $this->digestUri,
        $response_value,
        $this->qop,
        $this->authzid ? ',authzid="'.$this->_encode($this->authzid).'"' : ''
      );
    }
    
    /**
     * Set Qop
     *
     * @param   string qop
     */
    public function setQop($qop) {
      $this->qop= $qop;
    }

    /**
     * Get Qop
     *
     * @return  string
     */
    public function getQop() {
      return $this->qop;
    }

    /**
     * Set User
     *
     * @param   string user
     */
    public function setUser($user) {
      $this->user= $user;
    }

    /**
     * Get User
     *
     * @return  string
     */
    public function getUser() {
      return $this->user;
    }

    /**
     * Set Pass
     *
     * @param   string pass
     */
    public function setPass($pass) {
      $this->pass= $pass;
    }

    /**
     * Get Pass
     *
     * @return  string
     */
    public function getPass() {
      return $this->pass;
    }

    /**
     * Set Authzid
     *
     * @param   string authzid
     */
    public function setAuthzid($authzid) {
      $this->authzid= $authzid;
    }

    /**
     * Get Authzid
     *
     * @return  string
     */
    public function getAuthzid() {
      return $this->authzid;
    }

    /**
     * Set Nonce
     *
     * @param   string nonce
     */
    public function setNonce($nonce) {
      $this->nonce= $nonce;
    }

    /**
     * Get Nonce
     *
     * @return  string
     */
    public function getNonce() {
      return $this->nonce;
    }

    /**
     * Set Ncount
     *
     * @param   int ncount
     */
    public function setNcount($ncount) {
      $this->ncount= $ncount;
    }

    /**
     * Get Ncount
     *
     * @return  int
     */
    public function getNcount() {
      return $this->ncount;
    }

    /**
     * Set DigestUri
     *
     * @param   string digestUri
     */
    public function setDigestUri($digestUri) {
      $this->digestUri= $digestUri;
    }

    /**
     * Get DigestUri
     *
     * @return  string
     */
    public function getDigestUri() {
      return $this->digestUri;
    }

    /**
     * Set Charset
     *
     * @param   string charset
     */
    public function setCharset($charset) {
      $this->charset= $charset;
    }

    /**
     * Get Charset
     *
     * @return  string
     */
    public function getCharset() {
      return $this->charset;
    }
  }
?>
