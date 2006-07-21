<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */

  uses(
    'peer.URL',
    'peer.ftp.FtpDir', 
    'peer.SocketException', 
    'peer.ConnectException', 
    'peer.AuthenticationException'
  );
 
  /**
   * FTP connection
   *
   * Usage [retrieve directory listing]:
   * <code>
   *   $c= &new FtpConnection('ftp://user:pass@example.com');
   *   try(); {
   *     $c->connect();
   *     $d= &$c->getDir();
   *     var_dump($d);
   *   } if (catch('Exception', $e)) {
   *     $e->printStackTrace();
   *     $c->close();
   *     exit();
   *   }
   *   
   *   while ($entry= $d->getEntry()) {
   *     var_dump($entry);
   *   }
   *   $c->close();
   * </code>
   *
   * @see      rfc://959
   * @purpose  Wrap
   */
  class FtpConnection extends Object {
    public
      $url      = array(),
      $opt      = array();
      
    public
      $_hdl     = NULL;
      
    /**
     * Constructor
     *
     * Values for the parameter DSN:
     * <pre>
     * - ftp://user:pass@ftp.server/?timeout=3
     * - ftp://localhost
     * - ftp://anonymous:devnull@ftp.server:2121
     * - ftps://user@localhost
     * </pre>
     *
     * Note: SSL connect is only available if OpenSSL support is enabled 
     * into your version of PHP.
     *
     * Parameters and defaults:
     * <ul>
     *   <li>host: localhost</li>
     *   <li>port: 21</li>
     *   <li>timeout: 4 (seconds)</li>
     * </ul>
     *
     * @access  public
     * @param   string dsn
     */
    public function __construct($dsn) {
      $this->_dsn($dsn);
      
    }
    
    /**
     * Private helper function
     *
     * @access  private
     * @param   string dsn
     */
    public function _dsn($dsn) {
    
      // URL and defaults
      $this->url= &new URL($dsn);
      $this->url->getHost() || $this->url->_info['host']= 'localhost';
      $this->url->getPort() || $this->url->_info['port']= 21;
      
      // Options and defaults
      $this->opt= $this->url->getParams();
      $this->opt['timeout']= empty($this->opt['timeout']) ? 4 : $this->opt['timeout'];
    }

    /**
     * Connect (and log in, if necessary)
     *
     * @access  public  
     * @return  bool success
     * @throws  peer.ConnectException in case there's an error during connecting
     * @throws  peer.AuthenticationException when authentication fails
     */
    public function connect() {
      switch ($this->url->getScheme()) {
        case 'ftp':
          $this->_hdl= ftp_connect(
            $this->url->getHost(), 
            $this->url->getPort(), 
            $this->opt['timeout']
          );
          break;

        case 'ftps':
          $this->_hdl= ftp_ssl_connect(
            $this->url->getHost(), 
            $this->url->getPort(), 
            $this->opt['timeout']
          );
          break;
      }
      
      if (!is_resource($this->_hdl)) {
        throw(new ConnectException(sprintf(
          'Could not connect to %s:%d within %d seconds',
          $this->url->getHost(), $this->url->getPort(), $this->opt['timeout']
        )));
      }
      
      // User & password
      if (!$this->url->getUser()) return TRUE;
      
      if (FALSE === ftp_login($this->_hdl, $this->url->getUser(), $this->url->getPassword())) {
        throw(new AuthenticationException(sprintf(
          'Authentication failed for %s@%s (using password: %s)',
          $this->url->getUser(), $this->url->getHost(), $this->url->getPassword() ? 'no' : 'yes'
        )));
      }
      
      return TRUE;
    }
    
    /**
     * Disconnect
     *
     * @access  public
     * @return  bool success
     */
    public function close() {
      return ftp_close($this->_hdl);
    }
    
    /**
     * Get a directory object
     *
     * @access  public
     * @param   string dir default NULL directory name, defaults to working directory
     * @return  &peer.ftp.FtpDir
     * @throws  peer.SocketException
     */
    public function &getDir($dir= NULL) {
      if (NULL === $dir) {
        if (FALSE === ($dir= ftp_pwd($this->_hdl))) {
          throw(new SocketException('Cannot retrieve current directory'));
        }
      }
        
      return new FtpDir($dir, $this->_hdl);
    }
    
    /**
     * Set working directory
     *
     * @access  public
     * @param   &peer.ftp.FtpDir f
     * @throws  peer.SocketException
     * @return  bool success
     */
    public function setDir(&$f) {
      if (FALSE === ftp_chdir($this->_hdl, $f->name)) {
        throw(new SocketException('Cannot change directory to '.$f->name));
      }
      return TRUE;
    }

    /**
     * Create a directory
     *
     * @access  public
     * @param   &peer.ftp.FtpDir f
     * @return  bool success
     */
    public function makeDir(&$f) {
      return ftp_mkdir($this->_hdl, $f->name);
    }
    
    /**
     * Upload a file
     *
     * @access  public
     * @param   &mixed arg either a filename or an open File object
     * @param   string remote default NULL remote filename, will default to basename of arg
     * @param   string mode default FTP_ASCII (either FTP_ASCII or FTP_BINARY)
     * @return  bool success
     * @throws  peer.SocketException
     */
    public function put(&$arg, $remote= NULL, $mode= FTP_ASCII) {
      if (is_a($arg, 'File')) {
        $local= $arg->_fd;
        if (empty($remote)) $remote= basename ($arg->getUri());
        $f= 'ftp_fput';
      } else {
        $local= $arg;
        if (empty($remote)) $remote= basename($arg);
        $f= 'ftp_put';
      }
      if (FALSE === $f($this->_hdl, $remote, $local, $mode)) {
        throw(new SocketException(sprintf(
          'Could not put %s to %s using mode %s',
          $local, $remote, $mode
        )));
      }
      
      return TRUE;
    }

    /**
     * Download a file
     *
     * @access  public
     * @param   string remote remote filename
     * @param   &mixed arg either a filename or an open File object
     * @param   string mode default FTP_ASCII (either FTP_ASCII or FTP_BINARY)
     * @return  bool success
     * @throws  peer.SocketException
     */
    public function get($remote, &$arg, $mode= FTP_ASCII) {
      if (is_a($arg, 'File')) {
        $local= $arg->_fd;
        $f= 'ftp_fget';
      } else {
        $origin= $arg;
        $f= 'ftp_get';
      }
      if (FALSE === $f($this->_hdl, $local, $remote, $mode)) {
        throw(new SocketException(sprintf(
          'Could not get %s to %s using mode %s',
          $remote, $local, $mode
        )));
      }
      
      return TRUE;
    }
    
    /**
     * Deletes a file.
     *
     * @access  public
     * @param   string filename
     * @return  bool success
     */
    public function delete($remote) {
      return ftp_delete ($this->_hdl, $remote);
    }    

    /**
     * Renames a file in this directory.
     *
     * @access  public
     * @param   string source
     * @param   string target
     * @return  bool success
     */
    public function rename($src, $target) {
      return ftp_rename (
        $this->_hdl, 
        $src, 
        $target
      );
    }
    
    /**
     * Enables or disables the passive ftp mode. Call this after the inital
     * login.
     *
     * @access  public
     * @param   bool enable enable or disable passive mode
     * @return  bool success
     * @throws  peer.SocketException
     */
    public function setPassive($enable= TRUE) {
      if (NULL === $this->_hdl) {
        throw(new SocketException('Cannot change passive mode flag with no open connection'));
      }
      return ftp_pasv($this->_hdl, $enable);
    }
  }
?>
