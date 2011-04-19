<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'peer.Socket',
    'peer.sieve.SieveScript',
    'security.checksum.HMAC_MD5',
    'security.sasl.DigestChallenge',
    'util.log.Traceable'
  );

  // Authentication methods
  define('SIEVE_SASL_PLAIN',       'PLAIN');
  define('SIEVE_SASL_LOGIN',       'LOGIN');
  define('SIEVE_SASL_KERBEROS_V4', 'KERBEROS_V4');
  define('SIEVE_SASL_DIGEST_MD5',  'DIGEST-MD5');
  define('SIEVE_SASL_CRAM_MD5',    'CRAM-MD5');

  // Modules
  define('SIEVE_MOD_FILEINTO',     'FILEINTO');
  define('SIEVE_MOD_REJECT',       'REJECT');
  define('SIEVE_MOD_ENVELOPE',     'ENVELOPE');
  define('SIEVE_MOD_VACATION',     'VACATION');
  define('SIEVE_MOD_IMAPFLAGS',    'IMAPFLAGS');
  define('SIEVE_MOD_NOTIFY',       'NOTIFY');
  define('SIEVE_MOD_SUBADDRESS',   'SUBADDRESS');
  define('SIEVE_MOD_REGEX',        'REGEX');

  /**
   * Sieve is a mail filtering language
   *
   * Usage example [listing all available scripts]:
   * <code>
   *   uses('peer.sieve.SieveClient');
   *
   *   $s= new SieveClient('imap.example.com');
   *   $s->connect();
   *   $s->authenticate(SIEVE_SASL_PLAIN, 'user', 'password');
   *   var_export($s->getScripts());
   *   $s->close();
   * </code>
   *
   * Usage example [uploading a script from a local file]:
   * <code>
   *   uses('peer.sieve.SieveClient', 'io.File', 'io.FileUtil');
   *
   *   $s= new SieveClient('imap.example.com');
   *   $s->connect();
   *   $s->authenticate(SIEVE_SASL_PLAIN, 'user', 'password');
   *   with ($script= new SieveScript('myscript')); {
   *     $script->setCode(FileUtil::getContents(new File('myscript.txt')));
   *     $s->putScript($script);
   *   }
   *   $s->close();
   * </code>
   *
   * @see      rfc://3028 Sieve: A Mail Filtering Language
   * @see      rfc://3431 Sieve Extension: Relational Tests
   * @see      rfc://3598 Sieve Email Filtering -- Subaddress Extension
   * @see      rfc://2298 Extensible Message Format for Message Disposition Notifications (MDNs)
   * @see      http://www.cyrusoft.com/sieve/drafts/managesieve-04.txt 
   * @see      http://www.cyrusoft.com/sieve/
   * @purpose  Sieve Implementation
   */
  class SieveClient extends Object implements Traceable {
    public
      $cat      = NULL;

    public
      $_sock    = NULL,
      $_sinfo   = array();

    /**
     * Constructor
     *
     * @param   string host
     * @param   int port default 2000
     */  
    public function __construct($host, $port= 2000) {
      $this->_sock= new Socket($host, $port);
    }
  
    /**
     * Connect to sieve server
     *
     * @return  bool success
     * @throws  io.IOException in case connecting failed
     * @throws  lang.FormatException in case the response cannot be parsed
     */  
    public function connect() {
      $this->_sock->connect();
      
      // Read the banner message. Example:
      //
      // "IMPLEMENTATION" "Cyrus timsieved v1.0.0"
      // "SASL" "LOGIN PLAIN KERBEROS_V4 DIGEST-MD5 CRAM-MD5"
      // "SIEVE" "fileinto reject envelope vacation imapflags notify subaddress regex"
      // OK
      do {
        if (!($line= $this->_sock->readLine())) return FALSE;
        $this->cat && $this->cat->debug('<<<', $line);

        if ('OK' == substr($line, 0, 2)) {
          break;
        } else if ('"' == $line{0}) {
          sscanf($line, '"%[^"]" "%[^"]"', $key, $value);
          switch ($key) {
            case 'IMPLEMENTATION':
              $this->_sinfo[$key]= $value;
              break;

            case 'SASL':
            case 'SIEVE':
              $this->_sinfo[$key]= explode(' ', strtoupper($value));
              break;
            
            case 'STARTTLS':
              $this->_sinfo[$key]= TRUE;
              break;

            default:
              throw new FormatException('Cannot parse banner message line >'.$line.'<');
          }
          continue;
        }

        throw new FormatException('Unexpected response line >'.$line.'<');
      } while (1);
      
      $this->cat && $this->cat->debug('Server information:', $this->_sinfo);
      return TRUE;
    }
    
    /**
     * Wrapper that sends a command to the remote host.
     *
     * @param   string format
     * @param   var* args
     * @return  bool success
     */
    protected function _sendcmd() {
      $a= func_get_args();
      $cmd= vsprintf(array_shift($a), $a);
      $this->cat && $this->cat->debug('>>>', $cmd);
      return $this->_sock->write($cmd."\r\n");
    }

    /**
     * Wrapper that reads the response from the remote host, returning
     * it into an array if not specified otherwise.
     *
     * Stops reading at one of the terminals "OK", "NO" or "BYE".
     *
     * @param   bool discard default FALSE
     * @param   bool error default TRUE
     * @return  string[]
     * @throws  lang.FormatException in case "NO" occurs
     * @throws  peer.SocketException in case "BYE" occurs
     */
    protected function _response($discard= FALSE, $error= TRUE) {
      $lines= array();
      do {
        $line= $this->_sock->readLine();
        
        $this->cat && $this->cat->debug('<<<', $line);
        
        if ('OK' == substr($line, 0, 2)) {
          break;
        } else if ('NO' == substr($line, 0, 2)) {
          if (!$error) return FALSE;
          throw new FormatException(substr($line, 3));
        } else if ('BYE' == substr($line, 0, 3)) {
          throw new SocketException(substr($line, 4));
        } else if (!$discard) {
          $lines[]= $line;
        }
      } while (!$this->_sock->eof());

      return $discard ? TRUE : $lines;
    }
    
    /**
     * Return server implementation
     *
     * @return  string
     */
    public function getImplementation() {
      return $this->_sinfo['IMPLEMENTATION'];
    }

    /**
     * Retrieve supported modules. Return value is an array of modules
     * reported by the server consisting of the SIEVE_MOD_* constants.
     *
     * @return  string[] 
     */
    public function getSupportedModules() {
      return $this->_sinfo['SIEVE'];
    }

    /**
     * Check whether a specified module is supported
     *
     * @param   string method one of the SIEVE_MOD_* constants
     * @return  bool
     */
    public function supportsModule($module) {
      return in_array($module, $this->_sinfo['SIEVE']);
    }
    
    /**
     * Retrieve possible authentication methods. Return value is an 
     * array of supported methods reported by the server consisting
     * of the SIEVE_SASL_* constants.
     *
     * @return  string[] 
     */
    public function getAuthenticationMethods() {
      return $this->_sinfo['SASL'];
    }

    /**
     * Checks whether a specied authentication is available.
     *
     * @param   string method one of the SIEVE_SASL_* constants
     * @return  bool
     */
    public function hasAuthenticationMethod($method) {
      return in_array($method, $this->_sinfo['SASL']);
    }
    
    /**
     * Authenticate
     *
     * Supported methods:
     * <ul>
     *   <li>PLAIN</li>
     *   <li>LOGIN</li>
     *   <li>DIGEST-MD5</li>
     *   <li>CRAM-MD5</li>
     * </ul>
     *
     * @param   string method one of the SIEVE_SASL_* constants
     * @param   string user
     * @param   string pass
     * @param   string auth default NULL
     * @return  bool success
     * @throws  lang.IllegalArgumentException when the specified method is not supported
     */
    public function authenticate($method, $user, $pass, $auth= NULL) {
      if (!$this->hasAuthenticationMethod($method)) {
        throw new IllegalArgumentException('Authentication method '.$method.' not supported');
      }
      
      // Check whether we want to impersonate
      if (NULL === $auth) $auth= $user;
      
      // Send auth request depending on specified authentication method
      switch ($method) {
        case SIEVE_SASL_PLAIN:
          $e= base64_encode($auth."\0".$user."\0".$pass);
          $this->_sendcmd('AUTHENTICATE "PLAIN" {%d+}', strlen($e));
          $this->_sendcmd($e);
          break;

        case SIEVE_SASL_LOGIN:
          $this->_sendcmd('AUTHENTICATE "LOGIN"');
          $ue= base64_encode($user);
          $this->_sendcmd('{%d+}', strlen($ue));
          $this->_sendcmd($ue);
          $pe= base64_encode($pass);
          $this->_sendcmd('{%d+}', strlen($pe));
          $this->_sendcmd($pe);
          break;

        case SIEVE_SASL_DIGEST_MD5:
          $this->_sendcmd('AUTHENTICATE "DIGEST-MD5"');
          
          // Read server challenge. Example (decoded):
          // 
          // realm="example.com",nonce="GMybUaOM4lpMlJbeRwxOLzTalYDwLAxv/sLf8de4DPA=",
          // qop="auth,auth-int,auth-conf",cipher="rc4-40,rc4-56,rc4",charset=utf-8,
          // algorithm=md5-sess
          //
          // See also xp://security.sasl.DigestChallenge
          $len= $this->_sock->readLine(0x400);
          $str= base64_decode($this->_sock->readLine());
          $this->cat && $this->cat->debug('Challenge (length '.$len.'):', $str);

          $challenge= DigestChallenge::fromString($str);
          $response= $challenge->responseFor(DC_QOP_AUTH, $user, $pass, $auth);
          $this->cat && $this->cat->debug($challenge, $response);

          // Build and send challenge response
          $response->setDigestUri('sieve/'.$this->_sock->host);
          $cmd= $response->getString();          
          $this->cat && $this->cat->debug('Sending challenge response', $cmd);
          $this->_sendcmd('"%s"', base64_encode($cmd));

          // Finally, read the response auth
          $len= $this->_sock->readLine();
          $str= base64_decode($this->_sock->readLine());
          $this->cat && $this->cat->debug('Response auth (length '.$len.'):', $str);
          return TRUE;

        case SIEVE_SASL_CRAM_MD5:
          $this->_sendcmd('AUTHENTICATE "CRAM-MD5"');
          
          // Read server challenge. Example (decoded):
          // 
          // <2687127488.3645700@example.com>
          //
          // See also rfc://2195
          $len= $this->_sock->readLine(0x400);
          $challenge= base64_decode($this->_sock->readLine());
          $this->cat && $this->cat->debug('Challenge (length '.$len.'):', $challenge);
          
          // Build response and send it
          $response= sprintf(
            '%s %s',
            $user,
            bin2hex(HMAC_MD5::hash($challenge, $pass))
          );
          $this->cat && $this->cat->debug('Sending challenge response', $response);
          $this->_sendcmd('"%s"', base64_encode($response));
          break;

        default:
          throw new IllegalArgumentException('Authentication method '.$method.' not implemented');
      }
      
      // Read the response. Examples:
      //
      // - OK
      // - NO ("SASL" "authentication failure") "Authentication error"
      return $this->_response(TRUE);
    }
    
    /**
     * Retrieve a list of scripts stored on the server
     *
     * @return  peer.sieve.SieveScript[] scripts
     */
    public function getScripts() {
      $r= array();
      foreach ($this->getScriptNames() as $name => $info) {
        with ($s= $this->getScript($name)); {
          $s->setActive('ACTIVE' == $info);         // Only one at a time
        }
        $r[]= $s;
      }
      return $r;
    }

    /**
     * Retrieve a list of scripts names.
     *
     * @return  array
     */
    public function getScriptNames() {
      $this->_sendcmd('LISTSCRIPTS');
      
      // Response is something like this:
      //
      // "bigmessages"
      // "spam" ACTIVE
      $r= array();
      foreach ($this->_response() as $line) {
        if (!sscanf($line, '"%[^"]" %s', $name, $info)) continue;
        $r[$name]= $info;
      }
      return $r;
    }

    /**
     * Retrieve a script by its name
     *
     * @param   string name
     * @return  peer.sieve.SieveScript script
     */
    public function getScript($name) {
      $this->_sendcmd('GETSCRIPT "%s"', $name);
      if (!($r= $this->_response())) return $r;
      
      // Response is something like this:
      // 
      // {59} 
      // if size :over 100K { # this is a comment 
      //   discard; 
      // } 
      //
      // The number on the first line indicates the length. We simply 
      // discard this information.
      $s= new SieveScript($name);
      $s->setCode(implode("\n", array_slice($r, 1)));
      return $s;
    }

    /**
     * Delete a script from the server
     *
     * @param   string name
     * @return  bool success
     */
    public function deleteScript($name) {
      $this->_sendcmd('DELETESCRIPT "%s"', $name);
      return $this->_response(TRUE);
    }

    /**
     * Upload a script to the server
     *
     * @param   peer.sieve.SieveScript script
     * @return  bool success
     */
    public function putScript($script) {
      $this->_sendcmd('PUTSCRIPT "%s" {%d+}', $script->getName(), $script->getLength());
      $this->_sendcmd($script->getCode());
      return $this->_response(TRUE);
    }
    
    /**
     * Set a specific script as the active one on the server
     *
     * A user may have multiple Sieve scripts on the server, yet only one
     * script may be used for filtering of incoming messages. This is the
     * active script. Users may have zero or one active scripts and MUST
     * use the SETACTIVE command described below for changing the active
     * script or disabling Sieve processing. For example, a user may have
     * an everyday script they normally use and a special script they use
     * when they go on vacation. Users can change which script is being
     * used without having to download and upload a script stored somewhere
     * else.
     *
     * If the script name is the empty string (i.e. "") then any active 
     * script is disabled.
     *
     * @param   string name
     * @return  bool success
     */
    public function activateScript($name) {
      $this->_sendcmd('SETACTIVE "%s"', $name);
      return $this->_response(TRUE);
    }
    
    /**
     * Check whether there is enough space for a script to be uploaded
     *
     * @param   peer.sieve.SieveScript script
     * @return  bool success
     */
    public function hasSpaceFor($script) {
      $this->_sendcmd('HAVESPACE "%s" %d', $script->getName(), $script->getLength());
      return $this->_response(TRUE, FALSE);
    }
    
    /**
     * Close connection
     *
     */
    public function close() {
      $this->_sock->write("LOGOUT\r\n"); 
      $this->_sock->close();
      return TRUE;      
    }

    /**
     * Set a trace for debugging
     *
     * @param   util.log.LogCategory cat
     */
    public function setTrace($cat) { 
      $this->cat= $cat;
    }
  } 
?>
