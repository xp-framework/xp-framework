<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'peer.URL',
    'peer.ConnectException',
    'peer.AuthenticationException',
    'peer.SocketException',
    'peer.ftp.FtpDir',
    'peer.ftp.WindowsFtpListParser',
    'peer.ftp.DefaultFtpListParser'
  );

  /**
   * FTP client
   *
   * Usage example:
   * <code>
   *   $c= create(new FtpConnection('ftp://user:pass@example.com/'))->connect();
   *   
   *   // Retrieve root directory's listing
   *   Console::writeLine($c->rootDir()->entries());
   *
   *   $c->close();
   * </code>
   *
   * @see      rfc://959
   * @ext      ftp
   * @purpose  FTP protocol implementation
   */
  class FtpConnection extends Object {
    protected
      $url      = NULL,
      $root     = NULL;

    public
      $parser   = NULL,
      $handle   = NULL;

    /**
     * Constructor. Accepts a DSN of the following form:
     * <pre>
     *   {scheme}://[{user}:{password}@]{host}[:{port}]/[?{options}]
     * </pre>
     *
     * Scheme is one of the following:
     * <ul>
     *   <li>ftp (default)</li>
     *   <li>ftps (with SSL)</li>
     * </ul>
     *
     * Note: SSL connect is only available if OpenSSL support is enabled 
     * into your version of PHP.
     *
     * Options include:
     * <ul>
     *   <li>timeout - integer value indicating connection timeout in seconds, default: 4</li>
     *   <li>passive - boolean value controlling whether to use passive mode or not</li>
     * </ul>
     *
     * @param   string dsn
     */
    public function __construct($dsn) {
      $this->url= new URL($dsn);
    }
    
    /**
     * Connect (and log in, if necessary)
     *
     * @return  peer.ftp.FtpConnection this instance
     * @throws  peer.ConnectException in case there's an error during connecting
     * @throws  peer.AuthenticationException when authentication fails
     * @throws  peer.SocketException for general I/O failures
     */
    public function connect() {
      $host= $this->url->getHost();
      $port= $this->url->getPort(21);
      $timeout= $this->url->getParam('timeout', 4);

      switch ($this->url->getScheme()) {
        case 'ftp':
          $this->handle= ftp_connect($host, $port, $timeout);
          break;

        case 'ftps':
          $this->handle= ftp_ssl_connect($host, $port, $timeout);
          break;
      }
      
      if (!is_resource($this->handle)) {
        throw new ConnectException(sprintf(
          'Could not connect to %s:%d within %d seconds',
          $host, $port, $timeout
        ));
      }
      
      // User & password
      if ($this->url->getUser()) {
        if (FALSE === ftp_login($this->handle, $this->url->getUser(), $this->url->getPassword())) {
          throw new AuthenticationException(sprintf(
            'Authentication failed for %s@%s (using password: %s)',
            $this->url->getUser(), $host, $this->url->getPassword() ? 'yes' : 'no'
          ));
        }
      }

      // Set passive mode
      if (NULL !== ($pasv= $this->url->getParam('passive'))) {
        $this->setPassive((bool)$pasv);
      }
      
      // Setup list parser
      $this->setupListParser();
      
      // Retrieve root directory
      if (FALSE === ($dir= ftp_pwd($this->handle))) {
        throw new SocketException('Cannot retrieve current directory');
      }
      $this->root= new FtpDir($dir, $this);

      return $this;
    }

    /**
     * Setup directory list parser
     *
     */
    protected function setupListParser() {
      if ('Windows_NT' == ftp_systype($this->handle)) {
        $this->parser= new WindowsFtpListParser();
      } else {
        $this->parser= new DefaultFtpListParser();
      }
    }

    /**
     * Disconnect
     *
     * @return  bool success
     */
    public function close() {
      return ftp_close($this->handle);
    }

    /**
     * Enables or disables the passive ftp mode at runtime.
     *
     * @param   bool enable enable or disable passive mode
     * @return  bool success
     * @throws  peer.SocketException
     */
    public function setPassive($enable) {
      if (NULL === $this->handle) {
        throw new SocketException('Cannot change passive mode flag with no open connection');
      }
      return ftp_pasv($this->handle, $enable);
    }

    /**
     * Get root directory
     *
     * @return  peer.ftp.FtpDir
     */
    public function rootDir() {
      return $this->root;
    }

    /**
     * Sends a raw command to the FTP server and returns the server's
     * response (unparsed) as an array of strings.
     *
     * Accepts a command which will be handled as format-string for
     * further arbitrary arguments, e.g.:
     * <code>
     *   $c->sendCommand('CLNT %s', $clientName);
     * </code>
     *
     * @param   string command
     * @param   string* args
     * @return  string[] result
     * @throws  peer.SocketException in case of an I/O error
     */
    public function sendCommand($command) {
      if (func_num_args() > 1) {
        $args= func_get_args();
        $cmd= vsprintf($command, array_slice($args, 1));
      } else {
        $cmd= $command;
      }

      if (FALSE === ($response= ftp_raw($this->handle, $cmd))) {
        throw new SocketException('Failed sending command '.xp::stringOf($cmd));
      }
      return $response;
    }
  }
?>
