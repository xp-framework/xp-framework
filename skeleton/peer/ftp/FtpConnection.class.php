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
    'peer.ftp.DefaultFtpListParser',
    'util.log.Traceable'
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
   * @purpose  FTP protocol implementation
   */
  class FtpConnection extends Object implements Traceable {
    protected
      $url      = NULL,
      $root     = NULL,
      $passive  = FALSE,
      $cat      = NULL;

    public
      $parser   = NULL,
      $socket   = NULL;

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
          $this->socket= new Socket($host, $port);
          break;

        case 'ftps':
          $this->socket= new SSLSocket($host, $port);
          break;
      }
      
      $this->socket->connect();
      
      // Read banner message
      $this->expect($this->getResponse(), array(220));
      
      // User & password
      if ($this->url->getUser()) {
        try {
          $this->expect($this->sendCommand('USER %s', $this->url->getUser()), array(331));
          $this->expect($this->sendCommand('PASS %s', $this->url->getPassword()), array(230));
        } catch (ProtocolException $e) {
          $this->socket->close();
          throw new AuthenticationException(sprintf(
            'Authentication failed for %s@%s (using password: %s): %s',
            $this->url->getUser(), 
            $host, 
            $this->url->getPassword() ? 'yes' : 'no',
            $e->getMessage()
          ), $this->url->getUser(), $this->url->getPassword());
        }
      }

      // Set passive mode
      if (NULL !== ($pasv= $this->url->getParam('passive'))) {
        $this->setPassive((bool)$pasv);
      }
      
      // Setup list parser
      $this->setupListParser();
      
      // Retrieve root directory
      sscanf($this->expect($this->sendCommand('PWD'), array(257)), '"%[^"]"', $dir);
      $this->root= new FtpDir(strtr($dir, '\\', '/'), $this);

      return $this;
    }

    /**
     * Setup directory list parser
     *
     */
    protected function setupListParser() {
      $type= $this->expect($this->sendCommand('SYST'), array(215));
      if ('Windows_NT' == $type) {
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
      if ($this->socket) {
        try {
          $this->socket->eof() || $this->socket->write("QUIT\r\n");
        } catch (SocketException $ignored) {
          // Simply disconnect
        }
        $this->socket->close();
        $this->socket= NULL;
      }
      return TRUE;
    }
    
    /**
     * Retrieve transfer socket
     *
     * @return  peer.Socket
     */
    public function transferSocket() {
      $port= $this->expect($this->sendCommand('PASV'), array(227));
      $a= $p= array();
      sscanf($port, '%*[^(] (%d,%d,%d,%d,%d,%d)', $a[0], $a[1], $a[2], $a[3], $p[0], $p[1]);
        
      // Open transfer socket
      $transfer= new Socket(implode('.', $a), $p[0] * 256 + $p[1]);
      $transfer->connect();
      return $transfer;
    }

    /**
     * Enables or disables the passive ftp mode at runtime.
     *
     * @param   bool enable enable or disable passive mode
     * @return  bool success
     * @throws  peer.SocketException
     */
    public function setPassive($enable) {
      $this->passive= $enable;
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
     * Read response
     *
     * @return  string[]
     */
    public function getResponse() {
      $response= '';
      do {
        $response.= $this->socket->read();
      } while (!strstr($response, "\r\n"));
      $this->cat && $this->cat->debug('<<<', $response);
      return explode("\n", rtrim($response, "\r\n"));
    }
    
    /**
     * Check if return code meets expected response
     *
     * @param   string[] response
     * @param   int[] codes expected
     * @return  string message
     * @throws  peer.ProtocolException in case expectancy is not met
     */
    public function expect($r, $codes) {
      sscanf($r[0], "%d %[^\r\n]", $code, $message);
      if (!in_array($code, $codes)) {
        $error= sprintf(
          'Unexpected response [%d:%s], expecting %s',
          $code,
          $message,
          1 == sizeof($codes) ? $codes[0] : 'one of ('.implode(', ', $codes).')'
        );
        throw new ProtocolException($error);
      }
      return $message;
    }

    /**
     * Retrieve a listing of a given directory
     *
     * @param   string name the directory's name
     * @param   string options default NULL
     * @return  string[] list or NULL if nothing can be found
     * @throws  io.IOException
     */
    public function listingOf($name, $options= NULL) {
      with ($transfer= $this->transferSocket()); {
        $r= $this->sendCommand('LIST %s%s', $options ? $options.' ' : '', $name);
        sscanf($r[0], "%d %[^\r\n]", $code, $message);
        if (550 === $code) {          // Precondition failed
          $transfer->close();
          return NULL;
        } else if (150 === $code) {   // Listing
          $list= array();
          while ($line= $transfer->readLine()) {
            $list[]= $line;
          }
          $transfer->close();
          $r= $this->getResponse();
          sscanf($r[0], "%d %[^\r\n]", $code, $message);
          if (450 === $code) {        // No such file or directory
            return NULL;
          } else {
            $this->expect($r, array(226));
            return $list;
          }
        } else {                      // Unexpected response
          $transfer->close();
          throw new IOException('Listing '.$this->name.$name.' failed ('.$code.': '.$message.')');
        }
      }
    }

    /**
     * Sends a raw command to the FTP server and returns the server's
     * response (unparsed) as an array of strings.
     *
     * Accepts a command which will be socketd as format-string for
     * further arbitrary arguments, e.g.:
     * <code>
     *   $c->sendCommand('CLNT %s', $clientName);
     * </code>
     *
     * @param   string command
     * @param   string... args
     * @return  string[] result
     * @throws  peer.SocketException in case of an I/O error
     */
    public function sendCommand($command) {
      if (NULL === $this->socket) {
        throw new SocketException('Not connected');
      }

      if (func_num_args() > 1) {
        $args= func_get_args();
        $cmd= vsprintf($command, array_slice($args, 1));
      } else {
        $cmd= $command;
      }
      $this->cat && $this->cat->debug('>>>', $cmd);
      $this->socket->write($cmd."\r\n");
      return $this->getResponse();
    }

    /**
     * Get a directory object
     *
     * @deprecated Use FtpDir::getDir() instead!
     * @param   string dir default NULL directory name, defaults to working directory
     * @return  peer.ftp.FtpDir
     * @throws  peer.SocketException
     */
    public function getDir($dir= NULL) {
      raise('lang.MethodNotImplementedException', 'Deprecated', 'FtpConnection::getDir');
    }
    
    /**
     * Set working directory
     *
     * @deprecated Without replacement...
     * @param   peer.ftp.FtpDir f
     * @throws  peer.SocketException
     * @return  bool success
     */
    public function setDir($f) {
      raise('lang.MethodNotImplementedException', 'Deprecated', 'FtpConnection::setDir');
    }

    /**
     * Create a directory
     *
     * @deprecated Use FtpDir::newDir() instead!
     * @param   peer.ftp.FtpDir f
     * @return  bool success
     */
    public function makeDir($f) {
      raise('lang.MethodNotImplementedException', 'Deprecated', 'FtpConnection::makeDir');
    }
    
    /**
     * Upload a file
     *
     * @deprecated Use FtpFile::uploadFrom() instead!
     * @param   var arg either a filename or an open File object
     * @param   string remote default NULL remote filename, will default to basename of arg
     * @param   string mode default FTP_ASCII (either FTP_ASCII or FTP_BINARY)
     * @return  bool success
     * @throws  peer.SocketException
     */
    public function put($arg, $remote= NULL, $mode= FTP_ASCII) {
      raise('lang.MethodNotImplementedException', 'Deprecated', 'FtpConnection::put');
    }

    /**
     * Download a file
     *
     * @deprecated Use FtpFile::downloadTo() instead!
     * @param   string remote remote filename
     * @param   var arg either a filename or an open File object
     * @param   string mode default FTP_ASCII (either FTP_ASCII or FTP_BINARY)
     * @return  bool success
     * @throws  peer.SocketException
     */
    public function get($remote, $arg, $mode= FTP_ASCII) {
      raise('lang.MethodNotImplementedException', 'Deprecated', 'FtpConnection::get');
    }
    
    /**
     * Deletes a file.
     *
     * @deprecated Use FtpEntry::delete() instead!
     * @param   string filename
     * @return  bool success
     */
    public function delete($remote) {
      raise('lang.MethodNotImplementedException', 'Deprecated', 'FtpConnection::delete');
    }    

    /**
     * Renames a file in this directory.
     *
     * @deprecated Use FtpEntry::rename() instead!
     * @param   string source
     * @param   string target
     * @return  bool success
     */
    public function rename($src, $target) {
      raise('lang.MethodNotImplementedException', 'Deprecated', 'FtpConnection::rename');
    }
    
    /**
     * Sends a raw command directly to the FTP-Server.
     *
     * Please note, that the function does not parse whether the 
     * command was successful or not.
     *
     * @deprecated Use sendCommand() instead!
     * @param   string command
     * @return  array ServerResponse
     */
    public function quote($command) {
      raise('lang.MethodNotImplementedException', 'Deprecated', 'FtpConnection::quote');
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
