<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */

  uses(
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
   *   $smtp= new SmtpTransport();
   *   if (DEBUG) {
   *     $cat= Logger::getInstance()->getCategory();
   *     $cat->addAppender(new FileAppender('php://stderr'));
   *     $smtp->setTrace($cat);
   *   }
   *   try(); {
   *     $smtp->connect();            // Uses localhost:25 as default
   *     $smtp->send($msg);
   *   } catch (Exception $e) {
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
      $port  = 25;
      
    protected
      $_sock = NULL;
    
    /**
     * Private helper method
     *
     * @access  private
     * @param   string fmt or FALSE to indicate not to write any data
     * @param   string* args arguments for sprintf-string fmt
     * @param   mixed expect int for one possible returncode, int[] for multiple
     *          or FALSE to indicate not to read any data
     * @return  string buf
     */
    private function _sockcmd() {
      static $e;
      
      if (NULL === $this->_sock) return;
      if (isset($e)) throw ($e);
      
      // Arguments
      $args= func_get_args();
      $expect= (array)$args[sizeof($args)- 1];
      
      if (FALSE !== $args[0]) {
        $cmd= vsprintf($args[0], array_slice($args, 1, -1));
      
        // Write
        self::trace('>>>', $cmd);
        if (FALSE === $this->_sock->write($cmd."\n")) return FALSE;

        // Expecting data?
        if (FALSE === $expect[0]) return '';
      }
      
      // Read
      if (FALSE === ($buf= substr($this->_sock->read(), 0, -2))) return FALSE;
      self::trace('<<<', $buf);
      
      // Got expected data?
      $code= substr($buf, 0, 3);
      if (!in_array($code, $expect)) {
        trigger_error('Command: '.var_export($cmd, 1), E_USER_NOTICE);
        throw ($e= new FormatException(
          'Expected '.implode(' or ', $expect).', have '.$code.' ["'.$buf.'"]'
        ));
      }
      
      return $buf;
    }
    
    /**
     * Say hello (HELO or EHLO, dependant on SMTP variant)
     *
     * @access  protected
     * @return  bool success
     */
    protected function _hello() {
      if (!$this->ext) return self::_sockcmd('HELO %s', $this->me, 250);
      
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
      $ret= self::_sockcmd('EHLO %s', $this->me, 250);
      while ($ret && $buf= $this->_sock->read()) {
        if (2 != sscanf($buf, '%d-%s', $code, $option)) break;
        self::trace('+++', $code, $option);
        
        $this->opt[$option]= TRUE;
      }
      return $ret;
    }
    
    /**
     * Log in using AUTH PLAIN or AUTH LOGIN
     *
     * @access  protected
     * @return  bool success
     * @throws  IllegalArgumentException in case authentication method is not supported
     */
    protected function _login() {
      if (empty($this->auth)) return TRUE;
      
      switch (strtolower($this->auth)) {
        case SMTP_AUTH_LOGIN:
          self::_sockcmd('AUTH LOGIN', 334); 
          self::_sockcmd(base64_encode($this->user), 334);
          self::_sockcmd(base64_encode($this->pass), 235);
          break;
          
        case SMTP_AUTH_PLAIN:
          self::_sockcmd(
            'AUTH PLAIN %s', 
            base64_encode("\0".$this->user."\0".$this->pass),
            235
          ); 
          break;
          
        default:
          throw (new IllegalArgumentException(
            'Authentication method '.$this->auth.' not supported'
          ));
      }
    }
    
    /**
     * Parse DSN
     *
     * @access  private
     * @param   string dsn
     * @return  bool success
     */
    private function _parsedsn($dsn) {
      if (NULL === $dsn) return TRUE;
      
      $u= parse_url($dsn);
      if (!isset($u['host'])) {
        throw (new IllegalArgumentException('DSN parsing failed ["'.$dsn.'"]'));
      }
      
      // Scheme
      switch (strtoupper($u['scheme'])) {
        case 'ESMTP':
          $this->ext= TRUE;
          break;
          
        case 'SMTP':
          $this->ext= FALSE;
          break;
          
        default: 
          throw (new IllegalArgumentException('Scheme "'.$u['scheme'].'" not supported'));
      }
      
      // Copy host and port
      $this->host= $u['host'];
      $this->port= isset($u['port']) ? $u['port'] : 25;
      
      // Extra attributes
      if (isset($u['query'])) {
        parse_str($u['query'], $attr);
        $this->auth= isset($attr['auth']) ? $attr['auth'] : SMTP_AUTH_PLAIN;
      }

      // User & password
      if (isset($u['user'])) {
        $this->user= $u['user'];
        $this->pass= $u['pass'];
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
     * @access  public
     * @param   string dsn default NULL if omitted, 'smtp://localhost:25' will be assumed
     */
    public function connect($dsn= NULL) { 
      if (FALSE === self::_parsedsn($dsn)) return FALSE;
      
      $this->_sock= new Socket($this->host, $this->port);
      try {
        $this->_sock->connect();
        self::_sockcmd(FALSE, 220);            // Read banner message
        self::_hello();                        // Polite people say hello
        self::_login();                        // Log in
      } catch (XPException $e) {
        throw (new TransportException('Connect failed', $e));
      }
      
      return TRUE;
    }
    
    /**
     * Close connection
     *
     * @access  public
     */
    public function close() {
      if (NULL === $this->_sock) return;
      try {
        $this->_sock->write("QUIT\r\n"); 
        $this->_sock->close();
      } catch (XPException $e) {
        throw (new TransportException('Could not shutdown communications', $e));
      }
      
      return TRUE;      
    }
  
    /**
     * Send a message
     *
     * @access  public
     * @param   &peer.mail.Message message the Message object to send
     * @throws  TransportException to indicate an error occured
     */
    public function send(Message $message) {
      try {
        self::_sockcmd(
          'MAIL FROM: %s', 
          $message->from->localpart.'@'.$message->from->domain, 
          250
        ); 
        
        // List all recipients
        foreach(array(TO, CC, BCC) as $type) while ($r= $message->getRecipient($type)) {
          self::_sockcmd(
            'RCPT TO: %s', 
            $r->localpart.'@'.$r->domain, 
            array(250, 251)
          ); 
        }
        
        // Hide BCC
        $message->bcc= array();
        
        self::_sockcmd('DATA', 354);
        
        // Write headers
        self::_sockcmd($message->getHeaderString(), FALSE);
        
        // Write mail contents
        self::_sockcmd(preg_replace('/([\r\n]+)\.([\r\n]+)/', '$1..$2', $message->getBody()), FALSE);
        
        // Send a dot on a line by itself
        self::_sockcmd('.', 250);
        
      } catch (XPException $e) {
        throw (new TransportException('Send message failed', $e));
      }
      
      return TRUE;        
    }
  
  }
?>
