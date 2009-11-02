<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'peer.Socket',
    'text.encode.CvsPassword',
    'peer.ProtocolException',
    'peer.AuthenticationException',
    'util.log.Traceable'
  );

  define('CVSR_EXPECT_NONE',     'none');  
  define('CVSR_EXPECT_STRING',   'string');
  define('CVSR_EXPECT_FORMAT',   'format');
  define('CVSR_EXPECT_REGEX',    'regex');
  define('CVSR_EXPECT_OK',       'ok');
  define('CVSR_EXPECT_MESSAGE',  'message');
  define('CVSR_EXPECT_LIST',     'list');

  /**
   * CVS PServer client
   *
   * @experimental
   * @see      http://www.loria.fr/~molli/cvs/doc/cvsclient_toc.html 
   * @see      http://libcvs.cvshome.org/servlets/ProjectSource 
   * @purpose  CVS PServer protocol implementation
   */
  class PServerClient extends Object implements Traceable {
    public
      $cat      = NULL,
      $root     = '',
      $verbs    = array();

    public
      $_sock    = NULL;

    /**
     * Constructor
     *
     * @param   string host
     * @param   int port default 2401
     */  
    public function __construct($host, $port= 2401) {
      $this->_sock= new Socket($host, $port);
    }
    
    /**
     * Connect to CVS pserver
     *
     * @return  bool success
     * @throws  io.IOException in case connecting failed
     */  
    public function connect() {
      return $this->_sock->connect();
    }

    /**
     * Wrapper that sends a command to the remote host.
     *
     * @param   string format
     * @param   mixed* args
     * @return  bool success
     */
    protected function _sendcmd() {
      $a= func_get_args();
      $cmd= vsprintf(array_shift($a), $a);
      $this->cat && $this->cat->debug('>>>', $cmd);
      return $this->_sock->write($cmd."\n");
    }
    
    /**
     * Wrapper that reads a line and checks for expected output
     *
     * @param   string expect one of the CVSR_EXPECT_* constants
     * @param   string data default ''
     * @return  mixed
     * @throws  peer.ProtocolException in case expectation is not met
     * @see     http://www.loria.fr/~molli/cvs/doc/cvsclient_5.html#SEC14
     */
    protected function _readline($expect= CVSR_EXPECT_NONE, $data= '') {
      $line= $this->_sock->readLine();
      $this->cat && $this->cat->debug('<<<', $line);
      
      switch ($expect) {
        case CVSR_EXPECT_NONE:
          $met= TRUE;
          $return= $line;
          break;

        case CVSR_EXPECT_OK:
          $met= (0 == strncmp('ok', $line, 2));
          $return= TRUE;
          break;

        case CVSR_EXPECT_MESSAGE:
          if ($met= (0 == strncmp('M ', $line, 2))) {
            $return= substr($line, 2);
          }
          break;
          
        case CVSR_EXPECT_STRING:
          $met= (0 == strncmp($data, $line, strlen($data)));
          $return= $line;
          break;

        case CVSR_EXPECT_LIST:
          $met= (0 == strncmp($data, $line, strlen($data)));
          $return= explode(' ', substr($line, strlen($data)+ 1));
          break;
        
        case CVSR_EXPECT_FORMAT:
          $return= sscanf($line, $data);
          $met= !empty($return[0]);
          break;
        
        case CVSR_EXPECT_REGEX:
          $met= preg_match($data, $line, $return);
          break;
      }
      
      if (!$met) {
        throw new ProtocolException(sprintf(
          'Unexpected response "%s[...]", expecting (%s: "%s")',
          substr($line, 0, min(strlen($line), 20)),
          $expect,
          $data
        ));
      }
      return $return;
    }
    
    /**
     * Negotiate
     *
     * @return  bool success
     * @see     http://www.loria.fr/~molli/cvs/doc/cvsclient_5.html#SEC13
     */
    public function negotiate() {
      $this->_sendcmd('Root %s', $this->root);
      $this->_sendcmd('Valid-responses %s M E F MT', implode(' ', array(
        'Valid-requests',
        'New-entry',
        'Updated',
        'Created',
        'Update-existing',
        'Merged',
        'Rcs-diff',
        'Patched',
        'Mode',
        'Mod-time',
        'Checksum',
        'Copy-file',
        'Removed',
        'Remove-entry',
        'Set-static-directory',
        'Clear-static-directory',
        'Set-sticky',
        'Clear-sticky',
        'Template',
        'Set-checkin-prog',
        'Set-update-prog',
        'Notified',
        'Module-expansion',
        'Wrapper-rcsOption',
        'ok',
        'error',
        'Checked-in'
      )));
      $this->_sendcmd('valid-requests');
      $this->verbs= $this->_readline(CVSR_EXPECT_LIST, 'Valid-requests');
      return $this->_readline(CVSR_EXPECT_OK);
    }

    /**
     * Authenticate ourselves
     *
     * @param   string cvsroot
     * @param   string user
     * @param   string pass default ''
     * @return  bool success
     * @throws  peer.ProtocolException in case of a protocol error
     * @throws  peer.AuthenticationException in case login fails
     * @see     http://www.loria.fr/~molli/cvs/doc/cvsclient_3.html 
     */    
    public function login($cvsroot, $user, $pass= '') {
      $this->_sendcmd('BEGIN AUTH REQUEST');
      $this->_sendcmd($cvsroot);
      $this->_sendcmd($user);
      $this->_sendcmd('A%s', CvsPassword::encode($pass));
      $this->_sendcmd('END AUTH REQUEST');
      
      // Read server response
      //
      // Example #1 (success)
      // <<< I LOVE YOU
      // 
      // Example #2 (user does not exist)
      // <<< E Fatal error, aborting. 
      // <<< error 0 foo: no such user 

      // Example #3 (repository does not exist)
      // <<< error 0 /home/cvs/repositories/xp: no such repository 
      // <<< I HATE YOU 
      $error= $message= $code= NULL;
      while ($line= $this->_sock->readLine()) {
        $this->cat && $this->cat->debug('<<<', $line);
        if (0 == strcasecmp('I LOVE YOU', $line)) {
          
          // Authentication succeeded, we should now negotiate
          $this->root= $cvsroot;
          return $this->negotiate();
        } elseif (0 == strcasecmp('I HATE YOU', $line)) {
        
          // Terminal, break out of loop
          break;
        } elseif ('E' == $line{0}) {
        
          // Error message, read, remember and continue
          $error= substr($line, 2);
          continue;
        } elseif (sscanf($line, "error %d %[^\1]", $code, $message)) {

          // Error code and detail, read, remember and continue
          continue;        
        }
        throw new ProtocolException('Unexpected response "'.$line.'"');
      }
      
      // Authentication failed
      throw new AuthenticationException(
        sprintf('%d: %s (%s)', $code, $message, $error),
        $user,
        $pass
      );
    }
    
    /**
     * Return the version of CVS running as server.
     *
     * @return  string
     */
    public function version() {
      $this->_sendcmd('version');
      $version= $this->_readline(CVSR_EXPECT_MESSAGE);
      $this->_readline(CVSR_EXPECT_OK);
      return $version;
    }

    /**
     * Close connection
     *
     */
    public function close() {
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
