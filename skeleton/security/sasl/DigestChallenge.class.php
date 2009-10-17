<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('security.sasl.DigestResponse');

  // Quality of protection constants
  define('DC_QOP_AUTH',      'auth');
  define('DC_QOP_AUTH_INT',  'auth-int');
  define('DC_QOP_AUTH_CONF', 'auth-conf');
  
  // Cipher constants
  define('DC_CIPHER_DES',    'des');
  define('DC_CIPHER_3DES',   '3des');
  define('DC_CIPHER_RC4',    'rc4');
  define('DC_CIPHER_RC4_40', 'rc4-40');
  define('DC_CIPHER_RC4_56', 'rc4-56');

  /**
   * Digest challenge
   *
   * @see      rfc://2831
   * @purpose  Digest challenge wrapper
   */
  class DigestChallenge extends Object {
    public
      $realm        = '',
      $maxbuf       = 65536,
      $nonce        = '',
      $qop          = array(),
      $cipher       = array(),
      $charset      = NULL,
      $domain       = NULL,
      $algorithm    = NULL,
      $stale        = NULL,
      $opaque       = NULL;

    /**
     * Creates a DigestChallenge object from a string
     *
     * Example input string:
     * <pre>
     *   realm="example.com",nonce="GMybUaOM4lpMlJbeRwxOLzTalYDwLAxv/sLf8de4DPA=",
     *   qop="auth,auth-int,auth-conf",cipher="rc4-40,rc4-56,rc4",charset=utf-8,
     *   algorithm=md5-sess    
     * </pre>
     *
     * @param   string s
     * @return  security.sasl.DigestChallenge
     * @throws  lang.FormatException
     */
    public static function fromString($s) {
      with ($challenge= new DigestChallenge()); {
        $s.= ',';
        while ($p= strpos($s, '=')) {
          $key= substr($s, 0, $p);
          $t= ('"' == $s{$p+ 1}) ? strpos($s, '"', $p+ 2) + 1 : strpos($s, ',', $p+ 1);
          $value= trim(substr($s, $p+ 1, $t- $p- 1), '"');
          
          switch ($key) {
            case 'realm':
              $challenge->setRealm($value);
              break;

            case 'domain':
              $challenge->setDomain($value);
              break;

            case 'nonce':
              $challenge->setNonce($value);
              break;

            case 'qop':
              $challenge->setQop(explode(',', strtolower($value)));
              break;

            case 'cipher':
              $challenge->setCipher(explode(',', strtolower($value)));
              break;

            case 'charset':
              $challenge->setCharset($value);
              break;

            case 'algorithm':
              $challenge->setAlgorithm($value);
              break;

            case 'stale':
              $challenge->setStale(0 == strcasecmp($value, 'true'));
              break;

            case 'opaque':
              $challenge->setOpaque($value);
              break;

            case 'maxbuf':
              $challenge->setMaxbuf(intval($value));
              break;
            
            default:
              throw new FormatException('Unrecognized key "'.$key.'"');
          }
          $s= substr($s, $t+ 1);
        }
      }
      return $challenge;
    }
    
    /**
     * Returns the challenge response
     *
     * @param   string qop
     * @param   string user
     * @param   string pass
     * @param   string authzid default NULL
     * @return  security.sasl.DigestResponse
     * @throws  lang.FormatException
     */
    public function responseFor($qop, $user, $pass, $authzid= NULL) {
      if (!$this->hasQop($qop)) {
        throw new FormatException('Challenge does not contains DC_QOP_AUTH');
      }
      
      with ($r= new DigestResponse(
        $this->getRealm(), 
        $this->getNonce(), 
        $qop
      )); {
        $r->setUser($user);
        $r->setPass($pass);
        $r->setCharset($this->getCharset());
        
        // Only set authzid if specified
        $authzid && $r->setAuthzid($authzid);
      }
      return $r;
    }

    /**
     * Set Maxbuf
     *
     * @param   int maxbuf
     */
    public function setMaxbuf($maxbuf) {
      $this->maxbuf= $maxbuf;
    }

    /**
     * Get Maxbuf
     *
     * @return  int
     */
    public function getMaxbuf() {
      return $this->maxbuf;
    }
    
    /**
     * Set Realm
     *
     * @param   string realm
     */
    public function setRealm($realm) {
      $this->realm= $realm;
    }

    /**
     * Get Realm. Definition (realm):
     *
     * Mechanistically, a string which can enable users to know which
     * username and password to use, in case they might have different
     * ones for different servers. Conceptually, it is the name of a
     * collection of accounts that might include the user's account. This
     * string should contain at least the name of the host performing the
     * authentication and might additionally indicate the collection of
     * users who might have access. An example might be
     * "registered_users@gotham.news.example.com".  This directive is
     * optional; if not present, the client SHOULD solicit it from the
     * user or be able to compute a default; a plausible default might be
     * the realm supplied by the user when they logged in to the client
     * system. Multiple realm directives are allowed, in which case the
     * user or client must choose one as the realm for which to supply to
     * username and password.
     *
     * @return  string
     */
    public function getRealm() {
      return $this->realm;
    }

    /**
     * Set Domain
     *
     * @param   string domain
     */
    public function setDomain($domain) {
      $this->domain= $domain;
    }

    /**
     * Get Domain
     *
     * @return  string
     */
    public function getDomain() {
      return $this->domain;
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
     * Set Qop
     *
     * @param   mixed[] qop
     */
    public function setQop($qop) {
      $this->qop= $qop;
    }

    /**
     * Get Qop (the "quality of protection" values).  The value "auth"
     * indicates authentication; the value "auth-int" indicates
     * authentication with integrity protection; the value "auth-conf"
     * indicates authentication with integrity protection and encryption.
     *
     * @return  mixed[]
     */
    public function getQop() {
      return $this->qop;
    }
    
    /**
     * Check whether a specified qop is present
     *
     * @param   string qop one of the DC_QOP_* constants
     * @return  bool
     */
    public function hasQop($qop) {
      return in_array($qop, $this->qop);
    }

    /**
     * Set Cipher
     *
     * @param   mixed[] cipher
     */
    public function setCipher($cipher) {
      $this->cipher= $cipher;
    }

    /**
     * Get Cipher
     *
     * @return  mixed[]
     */
    public function getCipher() {
      return $this->cipher;
    }

    /**
     * Check whether a specified cipher value exists
     *
     * @param   string cipher one of the DC_CPIHER_* constants
     * @return  bool
     */
    public function hasCipher($cipher) {
      return in_array($cipher, $this->cipher);
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
     * This directive, if present, specifies that the server supports
     * UTF-8 encoding for the username and password. If not present, the
     * username and password must be encoded in ISO 8859-1 (of which
     * US-ASCII is a subset). The directive is needed for backwards
     * compatibility with HTTP Digest, which only supports ISO 8859-1.
     * This directive may appear at most once; if multiple instances are
     * present, the client should abort the authentication exchange.
     *
     * @return  string
     */
    public function getCharset() {
      return $this->charset;
    }

    /**
     * Set Algorithm
     *
     * @param   string algorithm
     */
    public function setAlgorithm($algorithm) {
      $this->algorithm= $algorithm;
    }

    /**
     * Get Algorithm
     *
     * @return  string
     */
    public function getAlgorithm() {
      return $this->algorithm;
    }

    /**
     * Set Stale
     *
     * @param   bool stale
     */
    public function setStale($stale) {
      $this->stale= $stale;
    }

    /**
     * Get Stale
     *
     * @return  bool
     */
    public function getStale() {
      return $this->stale;
    }

    /**
     * Set Opaque
     *
     * @param   string opaque
     */
    public function setOpaque($opaque) {
      $this->opaque= $opaque;
    }

    /**
     * Get Opaque
     *
     * @return  string
     */
    public function getOpaque() {
      return $this->opaque;
    }
  }
?>
