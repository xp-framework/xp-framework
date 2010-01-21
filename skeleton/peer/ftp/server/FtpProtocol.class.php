<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.Date',
    'util.DateUtil',
    'peer.BSDSocket',
    'peer.server.ServerProtocol',
    'peer.ftp.server.FtpSession',
    'peer.SocketException',
    'util.log.Traceable'
  );
  
  /**
   * Implement FTP server functionality
   *
   * @see      http://ipswitch.com/Support/WS_FTP-Server/guide/v4/A_FTPref4.html 
   * @see      xp://peer.server.ServerProtocol
   * @purpose  Connection listener
   */
  class FtpProtocol extends Object implements ServerProtocol, Traceable {
    const DATA_PASSIVE=    0x0001;
    const DATA_ACTIVE=     0x0002;
    const STRU_FILE=       'F';
    const STRU_RECORD=     'R';
    const STRU_PAGE=       'P';
    const MODE_STREAM=     'S';
    const MODE_BLOCK=      'B';
    const MODE_COMPRESSED= 'C';

    public
      $sessions         = array(),
      $cat              = NULL,
      $authenticator    = NULL,
      $storage          = NULL,
      $datasock         = array(),
      $interceptors     = array();

    /**
     * Constructor
     *
     * @param   peer.ftp.server.Storage storage
     * @param   peer.ftp.server.Authenticator authenticator
     */
    public function __construct($storage, $authenticator) {
      $this->storage= $storage;
      $this->authenticator= $authenticator;
    }

    /**
     * Initialize Protocol
     *
     * @return  bool
     */
    public function initialize() { }

    /**
     * Set a trace for debugging
     *
     * @param   util.log.LogCategory cat
     */
    public function setTrace($cat) { 
      $this->cat= $cat;
    }
    
    /**
     * Check all interceptors
     *
     * @param  peer.server.ConnectionEvent event The connection event
     * @param  peer.server.ftp.server.StorageEntry entry The storage entry
     * @param  string params The parameter string from request
     * @param  string method Interceptor method to invoke
     * @return bool
     */
    public function checkInterceptors($socket, $entry, $method) {
      if (!$this->interceptors) return TRUE;
    
      // Check each interceptors an it's conditions
      foreach ($this->interceptors as $intercept) {
        foreach ($intercept[0] as $condition) {
          if (!$condition->check($this->sessions[$socket->hashCode()], $entry)) {
            return TRUE;
          }
        }
        
        // Invoke interceptor method
        try {
          $intercept[1]->{$method}(
            $this->sessions[$socket->hashCode()],
            $entry
          );
        } catch (XPException $e) {
          $this->answer($socket, 550, 'Intercepted: '.$e->getMessage());
          return FALSE;
        }
      }
      return TRUE;
    }
    
    /**
     * Open the datasocket
     *
     * @param   peer.Socket socket
     * @return  peer.BSDSocket
     */
    public function openDatasock($socket) {

      // Client has neither sent a "PORT" nor "PASV" before calling LIST
      if (!isset($this->datasock[$socket->hashCode()])) {
        $this->answer($socket, 425, 'Unable to build data connection: Invalid argument');
        return NULL;        
      }

      if ($this->datasock[$socket->hashCode()] instanceof ServerSocket) {

        // Open socket in passive mode
        $this->cat && $this->cat->debug('+++ Opening passive connection');
        try {
          $dataSocket= $this->datasock[$socket->hashCode()]->accept();
        } catch (SocketException $e) {
          $this->answer($socket, 425, 'Cannot open passive connection '.$e->getMessage());
          return NULL;        
        }
      } else {
      
        // Open socket in active mode
        $this->cat && $this->cat->debug('+++ Opening active connection');
        with ($dataSocket= $this->datasock[$socket->hashCode()]); {
          try {
            $dataSocket->connect();
          } catch (SocketException $e) {
            $this->answer($socket, 425, 'Cannot open active connection '.$e->getMessage());
            return NULL;        
          }
        }
      }
      $this->cat && $this->cat->debug($dataSocket);
      return $dataSocket;
    }

    /**
     * Returns end of line identifier depending on the given type
     *
     * @param   char type
     * @return  string
     */
    public function eol($type) {
      return (TYPE_ASCII == $type) ? "\r\n" : "\n";
    }
    
    /**
     * Write an answer message to the socket
     *
     * @param   peer.Socket sock
     * @param   int code
     * @param   string text
     * @param   array lines default NULL lines of a multiline response
     * @return  int number of bytes written
     * @throws  io.IOException
     */
    public function answer($sock, $code, $text, $lines= NULL) {
      if (is_array($lines)) {
        $answer= $code.'-'.$text.":\r\n  ".implode("\n  ", $lines)."\r\n".$code." End\r\n";
      } else {
        $answer= sprintf("%d %s\r\n", $code, $text);
      }
      $this->cat && $this->cat->debug('<<< ', addcslashes($answer, "\0..\17"));
      return $sock->write($answer);
    }
    
    /**
     * Callback for the "USER" command
     *
     * @param   peer.Socket socket
     * @param   string params
     */
    public function onUser($socket, $params) {
      $this->sessions[$socket->hashCode()]->setUsername($params);
      $this->sessions[$socket->hashCode()]->setAuthenticated(FALSE);
      $this->answer($socket, 331, 'Password required for '.$params);
    }
    
    /**
     * Callback for the "PASS" command
     *
     * @param   peer.Socket socket
     * @param   string params
     */
    public function onPass($socket, $params) {
      with ($user= $this->sessions[$socket->hashCode()]->getUsername()); {
        try {
          $r= $this->authenticator->authenticate($user, $params);
        } catch (AuthenticatorException $e) {
          $this->answer($socket, 550, $e->getMessage());
          return;
        }

        // Did the authentication succeed?
        if (!$r) {
          $this->answer($socket, 530, 'Authentication failed for '.$user);
          return;
        }
        $this->answer($socket, 230, 'User '.$user.' logged in');
        $this->sessions[$socket->hashCode()]->setAuthenticated(TRUE);
      }
    }
    
    /**
     * REIN: This command terminates a USER, flushing all I/O and 
     * account information, except to allow any transfer in progress 
     * to be completed. A USER command may be expected to follow.
     *
     * @param   peer.Socket socket
     * @param   string params
     */
    public function onRein($socket, $params) {
      delete($this->datasock[$socket->hashCode()]);
      $this->sessions[$socket->hashCode()]->setAuthenticated(FALSE);
    }
        
    /**
     * Callback for the "PWD" command
     *
     * @param   peer.Socket socket
     * @param   string params
     */
    public function onPwd($socket, $params) {
      $this->answer($socket, 257, '"'.$this->storage->getBase($socket->hashCode()).'" is current directory');
    }

    /**
     * CWD: This command allows the user to work with a different 
     * directory or dataset without altering his login or account 
     * information.
     *
     * @param   peer.Socket socket
     * @param   string params
     */
    public function onCwd($socket, $params) {
      try {
        if (!($entry= $this->storage->lookup($socket->hashCode(), $params))) {
          $this->answer($socket, 550, $params.': No such file or directory');
          return;
        }

        if (!$this->checkInterceptors($socket, $entry, 'onCwd')) return;
        $pwd= $this->storage->setBase($socket->hashCode(), $params);
      } catch (XPException $e) {
        $this->answer($socket, 450, $e->getMessage());
        return;
      }
      $this->answer($socket, 250, '"'.$pwd.'" is new working directory');
    }

    /**
     * Change to the parent directory
     *
     * @param   peer.Socket socket
     * @param   string params
     */
    public function onCdup($socket, $params) {
      try {
        $pwd= $this->storage->setBase(
          $socket->hashCode(),
          dirname($this->storage->getBase($socket->hashCode()))
        );
      } catch (XPException $e) {
        $this->answer($socket, 550, $e->getMessage());
        return;
      }
      $this->answer($socket, 250, 'CDUP command successful');
    }

    /**
     * FEAT: This command causes the FTP server to list all new FTP 
     * features that the server supports beyond those described in 
     * RFC 959.
     *
     * @param   peer.Socket socket
     * @param   string params
     */
    public function onFeat($socket, $params) {
      $this->answer($socket, 211, 'Features', array('MDTM', 'SIZE'));
    }

    /**
     * HELP: This command causes the server to send a list of supported 
     * commands and other helpful information.
     *
     * @param   peer.Socket socket
     * @param   string params
     */
    public function onHelp($socket, $params) {
      $methods= array();
      $i= 0;
      foreach (get_class_methods($this) as $name) {
        if (0 != strncmp('on', $name, 2) || strlen($name) > 6) continue;

        if ($i++ % 8 == 0) $methods[++$offset]= '';
        $methods[$offset].= str_pad(strtoupper(substr($name, 2)), 8);
      }
      $this->answer($socket, 214, 'The following commands are recognized', $methods);
    }
    
    /**
     * SITE: This allows you to enter a command that is specific to the 
     * current FTP site.
     *
     * @param   peer.Socket socket
     * @param   string params
     */
    public function onSite($socket, $params) {
      $method= 'onSite'.strtolower(strtok($params, ' '));

      // Check if method is implemented and answer with code 550 in case
      // it isn't.
      if (!method_exists($this, $method)) {
        $this->answer($socket, 500, $command.' not understood');
        return;
      }

      $this->{$method}($socket, substr($params, strlen($method) - 6));
    }
    
    /**
     * SITE HELP
     *
     * @param   peer.Socket socket
     * @param   string params
     */
    public function onSiteHelp($socket, $params) {
      return $this->onHelp($socket, $params);
    }

    /**
     * SITE CHMOD
     *
     * @param   peer.Socket socket
     * @param   string params
     */
    public function onSiteChmod($socket, $params) {
      list($permissions, $uri)= explode(' ', trim($params), 2);
      $this->cat && $this->cat->warn($permissions);
      if (!($entry= $this->storage->lookup($socket->hashCode(), $uri))) {
        $this->answer($socket, 550, $uri.': No such file or directory');
        return;
      }
      
      $this->cat && $this->cat->debug($entry);
      
      $entry->setPermissions($permissions);
      $this->answer($socket, 200, 'SITE CHMOD command successful');
    }
   
    /**
     * Callback for the "SYST" command
     *
     * @param   peer.Socket socket
     * @param   string params
     */
    public function onSyst($socket, $params) {
      $this->answer($socket, 215, 'UNIX Type: L8');
    }

    /**
     * NOOP:  This command does not affect any parameters or previously 
     * entered commands. It specifies no action other than that the 
     * server send an OK reply.
     *
     * @param   peer.Socket socket
     * @param   string params
     */    
    public function onNoop($socket, $params) {
      $this->answer($socket, 200, 'OK');
    }

    /**
     * Helper method
     *
     * @param   int bits
     * @return  string
     */
    protected function _rwx($bits) {
      return (
        (($bits & 4) ? 'r' : '-').
        (($bits & 2) ? 'w' : '-').
        (($bits & 1) ? 'x' : '-')
      );
    }
    
    /**
     * Create a string representation from integer permissions
     *
     * @param   int permissions
     * @return  string
     */
    public function permissionString($permissions) {
      return (
        ($permissions & 0x4000 ? 'd' : '-').
        $this->_rwx(($permissions >> 6) & 7).
        $this->_rwx(($permissions >> 3) & 7).
        $this->_rwx(($permissions) & 7)
      );
    }
    
    /**
     * LIST: This command causes a list of file names and file details 
     * to be sent from the FTP site to the client.
     *
     * @param   peer.Socket socket
     * @param   string params
     */
    public function onList($socket, $params) {
      if (!$dataSocket= $this->openDatasock($socket)) return;
            
      // Split options from arguments
      if (($parts= sscanf($params, '-%s %s')) && $parts[0]) {
        $options= $parts[0];
        $params= $parts[1];
        $this->cat && $this->cat->debug('+++ Options:', $options);
      } else {
        $options= '';
      }
      
      if (!($entry= $this->storage->lookup($socket->hashCode(), $params))) {
        $this->answer($socket, 550, $params.': No such file or directory');
        $dataSocket->close();
        delete($dataSocket);
        $this->cat && $this->cat->debug($socket, $this->datasock[$socket->hashCode()]);
        return;
      }
      
      // Invoke interceptor
      if (!$this->checkInterceptors($socket, $entry, 'onRead')) {
        $dataSocket->close();
        return;
      }

      $this->answer($socket, 150, sprintf(
        'Opening %s mode data connection for filelist',
        $this->sessions[$socket->hashCode()]->typeName()
      ));
      
      // If a collection was specified, list its elements, otherwise,
      // list the single element
      if ($entry instanceof StorageCollection && !strstr($options, 'd')) {
        $elements= $entry->elements();
      } else {
        $elements= array($entry);
      }
      
      $before6Months= DateUtil::addMonths(Date::now(), -6)->getTime();
      for ($i= 0, $s= sizeof($elements); $i < $s; $i++) {
        $buf= sprintf(
          '%s  %2d %s  %s  %8d %s %s',
          $this->permissionString($elements[$i]->getPermissions()),
          $elements[$i]->numLinks(),
          $elements[$i]->getOwner(),
          $elements[$i]->getGroup(),
          $elements[$i]->getSize(),
          date(
            $elements[$i]->getModifiedStamp() < $before6Months ? 'M d  Y' : 'M d H:i',
            $elements[$i]->getModifiedStamp()
          ),
          $elements[$i]->getName()
        );
        $this->cat && $this->cat->debug('    ', $buf);
        $dataSocket->write($buf.$this->eol($this->sessions[$socket->hashCode()]->getType()));
      }
      $dataSocket->close();
      $this->answer($socket, 226, 'Transfer complete');
    }

    /**
     * NLST: This command causes a list of file names (with no other 
     * information) to be sent from the FTP site to the client.
     *
     * @param   peer.Socket socket
     * @param   string params
     */
    public function onNlst($socket, $params) {
      if (!$dataSocket= $this->openDatasock($socket)) return;

      // Split options from arguments
      if (($parts= sscanf($params, '-%s %s')) && $parts[0]) {
        $options= $parts[0];
        $params= $parts[1];
        $this->cat && $this->cat->debug('+++ Options:', $options);
      } else {
        $options= '';
      }
      
      if (!($entry= $this->storage->lookup($socket->hashCode(), $params))) {
        $this->answer($socket, 550, $params.': No such file or directory');
        $dataSocket->close();
        return;
      }
      
      // Invoke interceptor
      if (!$this->checkInterceptors($socket, $entry, 'onRead')) {
        $dataSocket->close();
        return;
      }

      $this->answer($socket, 150, sprintf(
        'Opening %s mode data connection for filelist',
        $this->sessions[$socket->hashCode()]->typeName()
      ));
      
      // If a collection was specified, list its elements, otherwise,
      // list the single element
      if ($entry instanceof StorageCollection && !strstr($options, 'd')) {
        $elements= $entry->elements();
      } else {
        $elements= array($entry);
      }
      
      for ($i= 0, $s= sizeof($elements); $i < $s; $i++) {
        $dataSocket->write(
          $elements[$i]->getName().
          $this->eol($this->sessions[$socket->hashCode()]->getType())
        );
      }
      $dataSocket->close();
      $this->answer($socket, 226, 'Transfer complete');
    }

    /**
     * MDTM: This command can be used to determine when a file in the 
     * server was last modified.
     *
     * @param   peer.Socket socket
     * @param   string params
     */
    public function onMdtm($socket, $params) {
      if (!($entry= $this->storage->lookup($socket->hashCode(), $params))) {
        $this->answer($socket, 550, $params.': No such file or directory');
        return;
      }
      
      // Invoke interceptor
      if (!$this->checkInterceptors($socket, $entry, 'onRead')) return;

      $this->answer($socket, 213, date('YmdHis', $entry->getModifiedStamp()));
    }

    /**
     * SIZE:  This command is used to obtain the transfer size of a file 
     * from the server: that is, the exact number of octets (8 bit bytes) 
     * which would be transmitted over the data connection should that 
     * file be transmitted. 
     *
     * @param   peer.Socket socket
     * @param   string params
     */
    public function onSize($socket, $params) {
      if (!($entry= $this->storage->lookup($socket->hashCode(), $params))) {
        $this->answer($socket, 550, $params.': No such file or directory');
        return;
      }
      
      // Invoke interceptor
      if (!$this->checkInterceptors($socket, $entry, 'onRead')) return;

      $this->answer($socket, 213, $entry->getSize());
    }

    /**
     * MKD:  This command causes the directory specified in pathname to 
     * be created as a directory (if pathname is absolute) or as a 
     * subdirectory of the current working directory (if pathname is 
     * relative).
     *
     * @param   peer.Socket socket
     * @param   string params
     */
    public function onMkd($socket, $params) {
      if ($this->storage->lookup($socket->hashCode(), $params)) {
        $this->answer($socket, 550, $params.': already exists');
        return;
      }
      
      // Invoke interceptor
      $entry= $this->storage->createEntry($socket->hashCode(), $params, ST_COLLECTION);
      if (!$this->checkInterceptors($socket, $entry, 'onCreate')) return;

      // Create the element
      try {
        $this->storage->create($socket->hashCode(), $params, ST_COLLECTION);
      } catch (XPException $e) {
        $this->answer($socket, 550, $params.': '.$e->getMessage());
        return;
      }
      $this->answer($socket, 257, $params.': successfully created');
    }

    /**
     * RMD: This command causes the directory specified in pathname to 
     * be removed as a directory (if pathname is absolute) or as a 
     * subdirectory of the current working directory (if pathname is 
     * relative).
     *
     * @param   peer.Socket socket
     * @param   string params
     */
    public function onRmd($socket, $params) {
      if (!($element= $this->storage->lookup($socket->hashCode(), $params))) {
        $this->answer($socket, 550, $params.': no such file or directory');
        return;
      }
      
      // Invoke interceptor
      if (!$this->checkInterceptors($socket, $element, 'onDelete')) return;

      // Delete the element
      try {
        $element->delete();
      } catch (XPException $e) {
        $this->answer($socket, 550, $params.': '.$e->getMessage());
        return;
      }
      $this->answer($socket, 250, $params.': successfully deleted');
    }

    /**
     * RETR: This command causes the server to transfer a copy of the 
     * file specified in pathname to the client. The status and contents 
     * of the file at the server site are unaffected.
     *
     * @param   peer.Socket socket
     * @param   string params
     */
    public function onRetr($socket, $params) {
      if (!$dataSocket= $this->openDatasock($socket)) return;
    
      if (!($entry= $this->storage->lookup($socket->hashCode(), $params))) {
        $this->answer($socket, 550, $params.': No such file or directory');
        $dataSocket->close();
        return;
      }
      $this->cat && $this->cat->debug($entry->toString());
      if ($entry instanceof StorageCollection) {
        $this->answer($socket, 550, $params.': is a directory');
        $dataSocket->close();
        return;
      }
      
      // Invoke interceptor
      if (!$this->checkInterceptors($socket, $entry, 'onRead')) {
        $dataSocket->close();
        return;
      }

      $this->answer($socket, 150, sprintf(
        'Opening %s mode data connection for %s (%d bytes)',
        $this->sessions[$socket->hashCode()]->getType(),
        $entry->getName(),
        $entry->getSize()
      ));
      try {
        $entry->open(SE_READ);
        while (!$dataSocket->eof() && $buf= $entry->read()) {
          if (!$dataSocket->write($buf)) break;
        }
        $entry->close();
      } catch (XPException $e) {
        $this->answer($socket, 550, $params.': '.$e->getMessage());
      } finally(); {
        $dataSocket->close();
        if ($e) return;
      }
      $this->answer($socket, 226, 'Transfer complete');
    }

    /**
     * Callback for the "STOR" command
     *
     * @param   peer.Socket socket
     * @param   string params
     */
    public function onStor($socket, $params) {
      if (!$dataSocket= $this->openDatasock($socket)) return;
      
      if (!($entry= $this->storage->lookup($socket->hashCode(), $params))) {

        // Invoke interceptor
        $entry= $this->storage->createEntry($socket->hashCode(), $params, ST_ELEMENT);
        if (!$this->checkInterceptors($socket, $entry, 'onCreate')) {
          $dataSocket->close();
          return;
        }

        try {
          $entry= $this->storage->create($socket->hashCode(), $params, ST_ELEMENT);
        } catch (XPException $e) {
          $this->answer($socket, 550, $params.': '.$e->getMessage());
          $dataSocket->close();
          return;
        }
      } else if ($entry instanceof StorageCollection) {
        $this->answer($socket, 550, $params.': is a directory');
        $dataSocket->close();
        return;
      }
      
      $this->answer($socket, 150, sprintf(
        'Opening %s mode data connection for %s',
        $this->sessions[$socket->hashCode()]->getType(),
        $entry->getName()
      ));
      try {
        $entry->open(SE_WRITE);
        while (!$dataSocket->eof() && $buf= $dataSocket->readBinary(32768)) {
          $entry->write($buf);
        }
        $entry->close();
      } catch (XPException $e) {
        $this->answer($socket, 550, $params.': '.$e->getMessage());
      } finally(); {
        $dataSocket->close();
        if ($e) return;
      }
      
      // Post check interception
      if (!$this->checkInterceptors($socket, $entry, 'onStored')) {
        $entry->delete();
        return;
      }

      $this->answer($socket, 226, 'Transfer complete');
    }

    /**
     * Callback for the "DELE" command
     *
     * @param   peer.Socket socket
     * @param   string params
     */
    public function onDele($socket, $params) {
      if (!($entry= $this->storage->lookup($socket->hashCode(), $params))) {
        $this->answer($socket, 550, $params.': No such file or directory');
        return;
      }
      if ($entry instanceof StorageCollection) {
        $this->answer($socket, 550, $params.': is a directory');
        return;
      }
      
      // Invoke interceptor
      if (!$this->checkInterceptors($socket, $entry, 'onDelete')) return;

      try {
        $entry->delete();
      } catch (IOException $e) {
        $this->answer($socket, 450, $params.': ', $e->getMessage());
        return;
      }

      $this->answer($socket, 250, $params.': file deleted');
    }
    
    /**
     * Rename a file from filename
     *
     * @param   peer.Socket socket
     * @param   string params
     */
    public function onRnfr($socket, $params) {
      if (!($entry= $this->storage->lookup($socket->hashCode(), $params))) {
        $this->answer($socket, 550, $params.': No such file or directory');
        return;
      }
      $this->cat && $this->cat->debug($entry);
      
      $this->sessions[$socket->hashCode()]->setTempVar('rnfr', $entry);
      $this->answer($socket, 350, 'File or directory exists, ready for destination name.');
    }
    
    /**
     * Rename a file into filename
     *
     * @param   peer.Socket socket
     * @param   string params
     */
    public function onRnto($socket, $params) {
      if (!$entry= $this->sessions[$socket->hashCode()]->getTempVar('rnfr')) {
        $this->answer($socket, 503, 'Bad sequence of commands');
        return;
      }
      
      // Invoke interceptor
      if (!$this->checkInterceptors($socket, $entry, 'onRename')) return;

      // Actually rename file
      $target= $this->storage->realname($socket->hashCode(), $params);
      try {
        $entry->rename($target);
        $this->cat && $this->cat->debug($params);
      } catch (XPException $e) {
        $this->answer($socket, 550, $params.': '. $e->getMessage());
        return;
      }
      
      $this->sessions[$socket->hashCode()]->removeTempVar('rnfr');
      $this->answer($socket, 250, 'Rename successful');
    }
    

    /**
     * Callback for the "TYPE" command
     *
     * @param   peer.Socket socket
     * @param   string params
     */
    public function onType($socket, $params) {
      switch ($params= strtoupper($params)) {
        case TYPE_ASCII:
        case TYPE_BINARY:
          $this->sessions[$socket->hashCode()]->setType($params);
          $this->answer($socket, 200, 'Type set to '.$params);
          break;
          
        default:
          $this->answer($socket, 550, 'Unknown type "'.$params.'"');
      }
    }

    /**
     * Callback for the "STRU" command
     *
     * @param   peer.Socket socket
     * @param   string params
     */
    public function onStru($socket, $params) {
      switch ($params= strtoupper($params)) {
        case self::STRU_FILE:
          $this->answer($socket, 200, 'Structure set to '.$params);
          break;
        
        case self::STRU_RECORD:
        case self::STRU_PAGE:
          $this->answer($socket, 504, $params.': unsupported structure type');
          break;
          
        default:
          $this->answer($socket, 501, $params.': unrecognized structure type');
      }
    }

    /**
     * Callback for the "MODE" command
     *
     * @param   peer.Socket socket
     * @param   string params
     */
    public function onMode($socket, $params) {
      switch ($params= strtoupper($params)) {
        case self::MODE_STREAM:
          $this->answer($socket, 200, 'Mode set to '.$params);
          break;
        
        case self::MODE_BLOCK:
        case self::STRU_COMPRESSED:
          $this->answer($socket, 504, $params.': unsupported transfer mode');
          break;
          
        default:
          $this->answer($socket, 501, $params.': unrecognized transfer mode');
      }
    }

    /**
     * Callback for the "QUIT" command
     *
     * @param   peer.Socket socket
     * @param   string params
     */
    public function onQuit($socket, $params) {
      $this->answer($socket, 221, 'Goodbye');
      $socket->close();
      
      // Kill associated session
      delete($this->sessions[$socket->hashCode()]);
    }

    /**
     * Callback for the "PORT" command
     *
     * @param   peer.Socket socket
     * @param   string params
     */
    public function onPort($socket, $params) {
      $this->mode[$socket->hashCode()]= self::DATA_ACTIVE;
      $octets= sscanf($params, '%d,%d,%d,%d,%d,%d');
      $host= sprintf('%s.%s.%s.%s', $octets[0], $octets[1], $octets[2], $octets[3]);
      $port= ($octets[4] * 256) + $octets[5];

      $this->cat && $this->cat->debug('+++ Host is ', $host);
      $this->cat && $this->cat->debug('+++ Port is ', $port);

      $this->datasock[$socket->hashCode()]= new BSDSocket($host, $port);
      $this->answer($socket, 200, 'PORT command successful');      
    }

    /**
     * Callback for the "OPTS" command
     *
     * @param   peer.Socket socket
     * @param   string params
     */
    public function onOpts($socket, $params) {
      if (2 != sscanf($params, '%s %s', $option, $value)) {
        $this->answer($socket, 501, 'OPTS: Invalid numer of arguments');
        return;
      }
      
      // Do not recognize any opts
      $this->answer($socket, 501, 'OPTS: '.$option.' not understood');
    }

    /**
     * Callback for the "PASV" command
     *
     * @param   peer.Socket socket
     * @param   string params
     */
    public function onPasv($socket, $params) {
      $this->mode[$socket->hashCode()]= self::DATA_PASSIVE;

      if ($this->datasock[$socket->hashCode()]) {
        $port= $this->datasock[$socket->hashCode()]->port;   // Recycle it!
      } else {      
        $port= rand(1000, 65536);
        $this->datasock[$socket->hashCode()]= new ServerSocket($this->server->socket->host, $port);
        try {
          $this->datasock[$socket->hashCode()]->create();
          $this->datasock[$socket->hashCode()]->bind();
          $this->datasock[$socket->hashCode()]->listen();
        } catch (IOException $e) {
          $this->answer($socket, 425, 'Cannot open passive connection '.$e->getMessage());
          delete($this->datasock[$socket->hashCode()]);
          return;
        }
      }
      $this->cat && $this->cat->debug('Passive mode: Data socket is', $this->datasock[$socket->hashCode()]);
      $octets= strtr(gethostbyname($this->server->socket->host), '.', ',').','.($port >> 8).','.($port & 0xFF);
      $this->answer($socket, 227, 'Entering passive mode ('.$octets.')');
    }
    
    /**
     * Handle client connect
     *
     * @param   peer.Socket socket
     */
    public function handleConnect($socket) {
      $this->cat && $this->cat->debugf('===> Client %s connected', $socket->host);

      // Create a new session object for this client
      $this->sessions[$socket->hashCode()]= new FtpSession();
      $this->answer($socket, 220, 'FTP server ready');
    }
    
    /**
     * Handle client data
     *
     * @param   peer.Socket socket
     * @return  mixed
     */
    public function handleData($socket)  {
      static $public= array('onhelp', 'onuser', 'onpass', 'onquit');

      $data= $socket->readLine();      
      $this->cat && $this->cat->debug('>>> ', addcslashes($data, "\0..\17"));
      sscanf($data, "%s %[^\r]", $command, $params);
      $method= 'on'.strtolower($command);

      // Check if method is implemented and answer with code 550 in case
      // it isn't.
      if (!method_exists($this, $method)) {
        $this->answer($socket, 500, $command.' not understood');
        return;
      }
      
      // Check if user needs to be logged in in order to execute this command
      if (
        !$this->sessions[$socket->hashCode()]->isAuthenticated() && 
        !in_array($method, $public)
      ) {
        $this->answer($socket, 530, 'Please log in first');
        return;
      }
      
      try {
        $this->{$method}($socket, $params);
      } catch (XPException $e) {
        $this->cat && $this->cat->warn('*** ', $e->toString());
        // Fall through
      }
      xp::gc();
    }
    
    /**
     * Handle client disconnect
     *
     * @param   peer.Socket socket
     */
    public function handleDisconnect($socket) {
      $this->cat && $this->cat->debugf('Client %s disconnected', $socket->host);
      
      // Kill associated session
      delete($this->sessions[$socket->hashCode()]);
    }

    /**
     * Handle I/O error
     *
     * @param   peer.Socket socket
     * @param   lang.XPException e
     */
    public function handleError($socket, $e) {
      $this->cat && $this->cat->debugf('Client %s I/O error', $socket->host, ' ~ ', $e);
      
      // Kill associated session
      delete($this->sessions[$socket->hashCode()]);
    }
  } 
?>
