<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */
 
  uses(
    'peer.URL',
    'peer.mail.transport.Transport',
    'peer.Socket'
  );
  
  // Authentication methods
  define('SMTP_AUTH_PLAIN', 'plain');
  define('SMTP_AUTH_LOGIN', 'login');
 
  /**
   * Mail transport via SMTP
   *
   * <code>
   *   define('DEBUG', 0);
   *
   *   $smtp= &new SmtpTransport();
   *   if (DEBUG) {
   *     $l= &Logger::getInstance();
   *     $cat= &$l->getCategory();
   *     $cat->addAppender(new FileAppender('php://stderr'));
   *     $smtp->setTrace($cat);
   *   }
   *   try(); {
   *     $smtp->connect();            // Uses localhost:25 as default
   *     $smtp->send($msg);
   *   } if (catch('Exception', $e)) {
   *     printf("Caught %s\n", $e->getClassName());
   *     $e->printStackTrace();
   *   }
   * 
   *   $smtp->close();
   * </code>
   *
   * @see      rfc://2822
   * @see      rfc://2554
   * @see      rfc://1891 SMTP Service Extension for Delivery Status Notifications
   * @see      http://www.sendmail.org/~ca/email/authrealms.html
   * @purpose  Provide a transport for SMTP/ESMTP
   */
  class SmtpTransport extends Transport {
    public
      $host  = '127.0.0.1',
      $me    = 'localhost',
      $user  = '',
      $pass  = '',
      $ext   = FALSE,
      $opt   = array(),
      $port  = 25,
      $auth  = NULL;
      
    public
      $_sock = NULL;
    
    /**
     * Private helper method
     *
     * @param   string fmt or FALSE to indicate not to write any data
     * @param   string* args arguments for sprintf-string fmt
     * @param   mixed expect int for one possible returncode, int[] for multiple
     *          or FALSE to indicate not to read any data
     * @return  string buf
     */
    protected function _sockcmd() {
      if (NULL === $this->_sock) return;
      
      // Arguments
      $args= func_get_args();
      $expect= (array)$args[sizeof($args)- 1];
      
      if (FALSE !== $args[0]) {
        $cmd= vsprintf($args[0], array_slice($args, 1, -1));
      
        // Write
        $this->trace('>>>', $cmd);
        if (FALSE === $this->_sock->write($cmd."\r\n")) return FALSE;

        // Expecting data?
        if (FALSE === $expect[0]) return '';
      }
      
      // Read
      if (FALSE === ($buf= substr($this->_sock->read(), 0, -2))) return FALSE;
      $this->trace('<<<', $buf);
      
      // Got expected data?
      $code= substr($buf, 0, 3);
      if (!in_array($code, $expect)) {
        throw(new FormatException(
          'Expected '.implode(' or ', $expect).', have '.$code.' ["'.$buf.'"]'
        ));
      }
      
      return $buf;
    }
    
    /**
     * Say hello (HELO or EHLO, dependant on SMTP variant)
     *
     * @return  bool success
     */
    protected function _hello() {
      if (!$this->ext) return $this->_sockcmd('HELO %s', $this->me, 250);
      
      // Example:
      // 
      // EHLO localhost
      // 250-friebes.net Hello localhost [127.0.0.1], pleased to meet you
      // 250-ENHANCEDSTATUSCODES
      // 250-PIPELINING
      // 250-8BITMIME
      // 250-SIZE
      // 250-DSN
      // 250-ETRN
      // 250-DELIVERBY
      // 250 HELP
      // 
      // 250-mrelayng.kundenserver.de Hello pd950b4d5.dip0.t-ipconnect.de [217.80.180.213]
 
      // 250-SIZE 
      // 250-PIPELINING 
      // 250-AUTH=PLAIN 
      // 250-AUTH 
      $ret= $this->_sockcmd('EHLO %s', $this->me, 250);
      while ($ret && $buf= $this->_sock->read()) {
        if (2 != sscanf($buf, '%d-%s', $code, $option)) break;
        $this->trace('+++', $code, $option);
        
        $this->opt[$option]= TRUE;
      }
      return $ret;
    }
    
    /**
     * Log in using AUTH PLAIN or AUTH LOGIN
     *
     * @return  bool success
     * @throws  lang.IllegalArgumentException in case authentication method is not supported
     */
    protected function _login() {
      if (empty($this->auth)) return TRUE;
      
      switch (strtolower($this->auth)) {
        case SMTP_AUTH_LOGIN:
          $this->_sockcmd('AUTH LOGIN', 334); 
          $this->_sockcmd(base64_encode($this->user), 334);
          $this->_sockcmd(base64_encode($this->pass), 235);
          break;
          
        case SMTP_AUTH_PLAIN:
          $this->_sockcmd(
            'AUTH PLAIN %s', 
            base64_encode("\0".$this->user."\0".$this->pass),
            235
          ); 
          break;
          
        default:
          throw(new IllegalArgumentException(
            'Authentication method '.$this->auth.' not supported'
          ));
      }
    }
    
    /**
     * Parse DSN
     *
     * @param   string dsn
     * @return  bool success
     */
    protected function _parsedsn($dsn) {
      if (NULL === $dsn) return TRUE;
      
      $u= new URL($dsn);
      if (!$u->getHost()) {
        throw(new IllegalArgumentException('DSN parsing failed ["'.$dsn.'"]'));
      }
      
      // Scheme
      switch (strtoupper($u->getScheme())) {
        case 'ESMTP':
          $this->ext= TRUE;
          break;
          
        case 'SMTP':
          $this->ext= FALSE;
          break;
          
        default: 
          throw(new IllegalArgumentException('Scheme "'.$u->getScheme().'" not supported'));
      }
      
      // Copy host and port
      $this->host= $u->getHost();
      $this->port= $u->getPort() ? $u->getPort() : 25;
      
      // User & password
      if ($u->getUser()) {
        $this->auth= $u->getParam('auth', SMTP_AUTH_PLAIN);
        $this->user= $u->getUser();
        $this->pass= $u->getPassword();
      }
    }
    
    /**
     * Connect to this transport
     *
     * DSN parameter examples:
     * <pre>
     *   smtp://localhost
     *   smtp://localhost:2525
     *   esmtp://user:pass@smtp.example.com:25/?auth=plain
     *   esmtp://user:pass@smtp.example.com:25/?auth=login
     * </pre>
     *
     * @param   string dsn default NULL if omitted, 'smtp://localhost:25' will be assumed
     */
    public function connect($dsn= NULL) { 
      if (FALSE === $this->_parsedsn($dsn)) return FALSE;
      
      $this->_sock= new Socket($this->host, $this->port);
      try {
        $this->_sock->connect();
        $this->_sockcmd(FALSE, 220);            // Read banner message
        $this->_hello();                        // Polite people say hello
        $this->_login();                        // Log in
      } catch (Exception $e) {
        throw(new TransportException('Connect failed', $e));
      }
      
      return TRUE;
    }
    
    /**
     * Close connection
     *
     */
    public function close() {
      if (NULL === $this->_sock) return;
      try {
        $this->_sock->write("QUIT\r\n"); 
        $this->_sock->close();
      } catch (Exception $e) {
        throw(new TransportException('Could not shutdown communications', $e));
      }
      
      return TRUE;      
    }
  
    /**
     * Send a message
     *
     * @param   peer.mail.Message message the Message object to send
     * @return  bool TRUE in case of success
     * @throws  peer.mail.transport.TransportException to indicate an error occured
     */
    public function send($message) {
      try {
        $this->_sockcmd(
          'MAIL FROM: %s', 
          $message->from->localpart.'@'.$message->from->domain, 
          250
        );

        // List all recipients, hide BCC
        foreach (array(TO, CC, BCC) as $type) while ($r= $message->getRecipient($type)) {
          $this->_sockcmd(
            'RCPT TO: %s', 
            $r->localpart.'@'.$r->domain, 
            array(250, 251)
          );
        }
        $message->bcc= array();

        if ($this->_sockcmd('DATA', 354)) {

          // Write headers
          $this->_sockcmd('%s', $message->getHeaderString(), FALSE);

          // Write mail contents. Make sure lines containing a dot by itself are
          // properly escaped.
          $this->_sockcmd('%s', preg_replace(
            '/(^|[\r\n])([\.]+)([\r\n]|$)/', 
            '$1.$2$3', 
            $message->getBody()
          ), FALSE);
        }
      } catch (Exception $e) {
        throw(new TransportException('Sending message failed', $e));
      }
      
      return (bool)$this->_sockcmd('.', 250);
    }
  }
?>
