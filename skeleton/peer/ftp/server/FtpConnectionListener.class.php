<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.Date',
    'util.DateUtil',
    'peer.BSDSocket',
    'peer.server.ConnectionListener',
    'peer.ftp.server.FtpSession',
    'peer.SocketException',
    'util.log.Traceable'
  );
  
  define('DATA_PASSIVE',    0x0001);
  define('DATA_ACTIVE',     0x0002);
  define('STRU_FILE',       'F');
  define('STRU_RECORD',     'R');
  define('STRU_PAGE',       'P');
  define('MODE_STREAM',     'S');
  define('MODE_BLOCK',      'B');
  define('MODE_COMPRESSED', 'C');
  
  /**
   * Implement FTP server functionality
   *
   * @see      http://ipswitch.com/Support/WS_FTP-Server/guide/v4/A_FTPref4.html 
   * @see      xp://peer.server.ConnectionListener
   * @purpose  Connection listener
   */
  class FtpConnectionListener extends ConnectionListener implements Traceable {
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
    public function checkInterceptors($event, $entry, $method) {
      if (!$this->interceptors) return TRUE;
    
      // Check each interceptors an it's conditions
      foreach ($this->interceptors as $intercept) {
        foreach ($intercept[0] as $condition) {
          if (!$condition->check($this->sessions[$event->stream->hashCode()], $entry)) {
            return TRUE;
          }
        }
        
        // Invoke interceptor method
        try {
          $intercept[1]->{$method}(
            $this->sessions[$event->stream->hashCode()],
            $entry
          );
        } catch (XPException $e) {
          $this->answer($event->stream, 550, 'Intercepted: '.$e->getMessage());
          return FALSE;
        }
      }
      return TRUE;
    }
    
    /**
     * Open the datasocket
     *
     * @param   peer.server.ConnectionEvent event
     * @return  peer.BSDSocket
     */
    public function openDatasock($event) {

      // Client has neither sent a "PORT" nor "PASV" before calling LIST
      if (!isset($this->datasock[$event->stream->hashCode()])) {
        $this->answer($event->stream, 425, 'Unable to build data connection: Invalid argument');
        return NULL;        
      }

      if ($this->datasock[$event->stream->hashCode( instanceof ServerSocket])) {

        // Open socket in passive mode
        $this->cat && $this->cat->debug('+++ Opening passive connection');
        try {
          $socket= $this->datasock[$event->stream->hashCode()]->accept();
        } catch (SocketException $e) {
          $this->answer($event->stream, 425, 'Cannot open passive connection '.$e->getMessage());
          return NULL;        
        }
      } else {
      
        // Open socket in active mode
        $this->cat && $this->cat->debug('+++ Opening active connection');
        with ($socket= $this->datasock[$event->stream->hashCode()]); {
          try {
            $socket->connect();
          } catch (SocketException $e) {
            $this->answer($event->stream, 425, 'Cannot open active connection '.$e->getMessage());
            return NULL;        
          }
        }
      }
      $this->cat && $this->cat->debug($socket);
      return $socket;
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
     * @param   peer.server.ConnectionEvent event
     * @param   string params
     */
    public function onUser($event, $params) {
      $this->sessions[$event->stream->hashCode()]->setUsername($params);
      $this->sessions[$event->stream->hashCode()]->setAuthenticated(FALSE);
      $this->answer($event->stream, 331, 'Password required for '.$params);
    }
    
    /**
     * Callback for the "PASS" command
     *
     * @param   peer.server.ConnectionEvent event
     * @param   string params
     */
    public function onPass($event, $params) {
      with ($user= $this->sessions[$event->stream->hashCode()]->getUsername()); {
        try {
          $r= $this->authenticator->authenticate($user, $params);
        } catch (AuthenticatorException $e) {
          $this->answer($event->stream, 550, $e->getMessage());
          return;
        }

        // Did the authentication succeed?
        if (!$r) {
          $this->answer($event->stream, 530, 'Authentication failed for '.$user);
          return;
        }
        $this->answer($event->stream, 230, 'User '.$user.' logged in');
        $this->sessions[$event->stream->hashCode()]->setAuthenticated(TRUE);
      }
    }
    
    /**
     * REIN: This command terminates a USER, flushing all I/O and 
     * account information, except to allow any transfer in progress 
     * to be completed. A USER command may be expected to follow.
     *
     * @param   peer.server.ConnectionEvent event
     * @param   string params
     */
    public function onRein($event, $params) {
      delete($this->datasock[$event->stream->hashCode()]);
      $this->sessions[$event->stream->hashCode()]->setAuthenticated(FALSE);
    }
        
    /**
     * Callback for the "PWD" command
     *
     * @param   peer.server.ConnectionEvent event
     * @param   string params
     */
    public function onPwd($event, $params) {
      $this->answer($event->stream, 257, '"'.$this->storage->getBase($event->stream->hashCode()).'" is current directory');
    }

    /**
     * CWD: This command allows the user to work with a different 
     * directory or dataset without altering his login or account 
     * information.
     *
     * @param   peer.server.ConnectionEvent event
     * @param   string params
     */
    public function onCwd($event, $params) {
      try {
        if (!($entry= $this->storage->lookup($event->stream->hashCode(), $params))) {
          $this->answer($event->stream, 550, $params.': No such file or directory');
          return;
        }

        if (!$this->checkInterceptors($event, $entry, 'onCwd')) return;
        $pwd= $this->storage->setBase($event->stream->hashCode(), $params);
      } catch (XPException $e) {
        $this->answer($event->stream, 450, $e->getMessage());
        return;
      }
      $this->answer($event->stream, 250, '"'.$pwd.'" is new working directory');
    }

    /**
     * Change to the parent directory
     *
     * @param   peer.server.ConnectionEvent event
     * @param   string params
     */
    public function onCdup($event, $params) {
      try {
        $pwd= $this->storage->setBase(
          $event->stream->hashCode(),
          dirname($this->storage->getBase($event->stream->hashCode()))
        );
      } catch (XPException $e) {
        $this->answer($event->stream, 550, $e->getMessage());
        return;
      }
      $this->answer($event->stream, 250, 'CDUP command successful');
    }

    /**
     * FEAT: This command causes the FTP server to list all new FTP 
     * features that the server supports beyond those described in 
     * RFC 959.
     *
     * @param   peer.server.ConnectionEvent event
     * @param   string params
     */
    public function onFeat($event, $params) {
      $this->answer($event->stream, 211, 'Features', array('MDTM', 'SIZE'));
    }

    /**
     * HELP: This command causes the server to send a list of supported 
     * commands and other helpful information.
     *
     * @param   peer.server.ConnectionEvent event
     * @param   string params
     */
    public function onHelp($event, $params) {
      $methods= array();
      $i= 0;
      foreach (get_class_methods($this) as $name) {
        if (0 != strncmp('on', $name, 2) || strlen($name) > 6) continue;

        if ($i++ % 8 == 0) $methods[++$offset]= '';
        $methods[$offset].= str_pad(strtoupper(substr($name, 2)), 8);
      }
      $this->answer($event->stream, 214, 'The following commands are recognized', $methods);
    }
    
    /**
     * SITE: This allows you to enter a command that is specific to the 
     * current FTP site.
     *
     * @param   peer.server.ConnectionEvent event
     * @param   string params
     */
    public function onSite($event, $params) {
      $method= 'onSite'.strtolower(strtok($params, ' '));

      // Check if method is implemented and answer with code 550 in case
      // it isn't.
      if (!method_exists($this, $method)) {
        $this->answer($event->stream, 500, $command.' not understood');
        return;
      }

      $this->{$method}($event, substr($params, strlen($method) - 6));
    }
    
    /**
     * SITE HELP
     *
     * @param   peer.server.ConnectionEvent event
     * @param   string params
     */
    public function onSiteHelp($event, $params) {
      return $this->onHelp($event, $params);
    }

    /**
     * SITE CHMOD
     *
     * @param   peer.server.ConnectionEvent event
     * @param   string params
     */
    public function onSiteChmod($event, $params) {
      list($permissions, $uri)= explode(' ', trim($params), 2);
      $this->cat->warn($permissions);
      if (!($entry= $this->storage->lookup($event->stream->hashCode(), $uri))) {
        $this->answer($event->stream, 550, $uri.': No such file or directory');
        return;
      }
      
      $this->cat->debug($entry);
      
      $entry->setPermissions($permissions);
      $this->answer($event->stream, 200, 'SITE CHMOD command successful');
    }
   
    /**
     * Callback for the "SYST" command
     *
     * @param   peer.server.ConnectionEvent event
     * @param   string params
     */
    public function onSyst($event, $params) {
      $this->answer($event->stream, 215, 'UNIX Type: L8');
    }

    /**
     * NOOP:  This command does not affect any parameters or previously 
     * entered commands. It specifies no action other than that the 
     * server send an OK reply.
     *
     * @param   peer.server.ConnectionEvent event
     * @param   string params
     */    
    public function onNoop($event, $params) {
      $this->answer($event->stream, 200, 'OK');
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
     * @param   peer.server.ConnectionEvent event
     * @param   string params
     */
    public function onList($event, $params) {
      if (!$socket= $this->openDatasock($event)) return;
            
      // Split options from arguments
      if (($parts= sscanf($params, '-%s %s')) && $parts[0]) {
        $options= $parts[0];
        $params= $parts[1];
        $this->cat && $this->cat->debug('+++ Options:', $options);
      } else {
        $options= '';
      }
      
      if (!($entry= $this->storage->lookup($event->stream->hashCode(), $params))) {
        $this->answer($event->stream, 550, $params.': No such file or directory');
        $socket->close();
        delete($socket);
        $this->cat && $this->cat->debug($socket, $this->datasock[$event->stream->hashCode()]);
        return;
      }
      
      // Invoke interceptor
      if (!$this->checkInterceptors($event, $entry, 'onRead')) {
        $socket->close();
        return;
      }

      $this->answer($event->stream, 150, sprintf(
        'Opening %s mode data connection for filelist',
        $this->sessions[$event->stream->hashCode()]->typeName()
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
        $socket->write($buf.$this->eol($this->sessions[$event->stream->hashCode()]->getType()));
      }
      $socket->close();
      $this->answer($event->stream, 226, 'Transfer complete');
    }

    /**
     * NLST: This command causes a list of file names (with no other 
     * information) to be sent from the FTP site to the client.
     *
     * @param   peer.server.ConnectionEvent event
     * @param   string params
     */
    public function onNlst($event, $params) {
      if (!$socket= $this->openDatasock($event)) return;

      // Split options from arguments
      if (($parts= sscanf($params, '-%s %s')) && $parts[0]) {
        $options= $parts[0];
        $params= $parts[1];
        $this->cat && $this->cat->debug('+++ Options:', $options);
      } else {
        $options= '';
      }
      
      if (!($entry= $this->storage->lookup($event->stream->hashCode(), $params))) {
        $this->answer($event->stream, 550, $params.': No such file or directory');
        $socket->close();
        return;
      }
      
      // Invoke interceptor
      if (!$this->checkInterceptors($event, $entry, 'onRead')) {
        $socket->close();
        return;
      }

      $this->answer($event->stream, 150, sprintf(
        'Opening %s mode data connection for filelist',
        $this->sessions[$event->stream->hashCode()]->typeName()
      ));
      
      // If a collection was specified, list its elements, otherwise,
      // list the single element
      if ($entry instanceof StorageCollection && !strstr($options, 'd')) {
        $elements= $entry->elements();
      } else {
        $elements= array($entry);
      }
      
      for ($i= 0, $s= sizeof($elements); $i < $s; $i++) {
        $socket->write(
          $elements[$i]->getName().
          $this->eol($this->sessions[$event->stream->hashCode()]->getType())
        );
      }
      $socket->close();
      $this->answer($event->stream, 226, 'Transfer complete');
    }

    /**
     * MDTM: This command can be used to determine when a file in the 
     * server was last modified.
     *
     * @param   peer.server.ConnectionEvent event
     * @param   string params
     */
    public function onMdtm($event, $params) {
      if (!($entry= $this->storage->lookup($event->stream->hashCode(), $params))) {
        $this->answer($event->stream, 550, $params.': No such file or directory');
        return;
      }
      
      // Invoke interceptor
      if (!$this->checkInterceptors($event, $entry, 'onRead')) return;

      $this->answer($event->stream, 213, date('YmdHis', $entry->getModifiedStamp()));
    }

    /**
     * SIZE:  This command is used to obtain the transfer size of a file 
     * from the server: that is, the exact number of octets (8 bit bytes) 
     * which would be transmitted over the data connection should that 
     * file be transmitted. 
     *
     * @param   peer.server.ConnectionEvent event
     * @param   string params
     */
    public function onSize($event, $params) {
      if (!($entry= $this->storage->lookup($event->stream->hashCode(), $params))) {
        $this->answer($event->stream, 550, $params.': No such file or directory');
        return;
      }
      
      // Invoke interceptor
      if (!$this->checkInterceptors($event, $entry, 'onRead')) return;

      $this->answer($event->stream, 213, $entry->getSize());
    }

    /**
     * MKD:  This command causes the directory specified in pathname to 
     * be created as a directory (if pathname is absolute) or as a 
     * subdirectory of the current working directory (if pathname is 
     * relative).
     *
     * @param   peer.server.ConnectionEvent event
     * @param   string params
     */
    public function onMkd($event, $params) {
      if ($this->storage->lookup($event->stream->hashCode(), $params)) {
        $this->answer($event->stream, 550, $params.': already exists');
        return;
      }
      
      // Invoke interceptor
      $entry= $this->storage->createEntry($event->stream->hashCode(), $params, ST_COLLECTION);
      if (!$this->checkInterceptors($event, $entry, 'onCreate')) return;

      // Create the element
      try {
        $this->storage->create($event->stream->hashCode(), $params, ST_COLLECTION);
      } catch (XPException $e) {
        $this->answer($event->stream, 550, $params.': '.$e->getMessage());
        return;
      }
      $this->answer($event->stream, 257, $params.': successfully created');
    }

    /**
     * RMD: This command causes the directory specified in pathname to 
     * be removed as a directory (if pathname is absolute) or as a 
     * subdirectory of the current working directory (if pathname is 
     * relative).
     *
     * @param   peer.server.ConnectionEvent event
     * @param   string params
     */
    public function onRmd($event, $params) {
      if (!($element= $this->storage->lookup($event->stream->hashCode(), $params))) {
        $this->answer($event->stream, 550, $params.': no such file or directory');
        return;
      }
      
      // Invoke interceptor
      if (!$this->checkInterceptors($event, $element, 'onDelete')) return;

      // Delete the element
      try {
        $element->delete();
      } catch (XPException $e) {
        $this->answer($event->stream, 550, $params.': '.$e->getMessage());
        return;
      }
      $this->answer($event->stream, 250, $params.': successfully deleted');
    }

    /**
     * RETR: This command causes the server to transfer a copy of the 
     * file specified in pathname to the client. The status and contents 
     * of the file at the server site are unaffected.
     *
     * @param   peer.server.ConnectionEvent event
     * @param   string params
     */
    public function onRetr($event, $params) {
      if (!$socket= $this->openDatasock($event)) return;
    
      if (!($entry= $this->storage->lookup($event->stream->hashCode(), $params))) {
        $this->answer($event->stream, 550, $params.': No such file or directory');
        $socket->close();
        return;
      }
      $this->cat && $this->cat->debug($entry->toString());
      if ($entry instanceof StorageCollection) {
        $this->answer($event->stream, 550, $params.': is a directory');
        $socket->close();
        return;
      }
      
      // Invoke interceptor
      if (!$this->checkInterceptors($event, $entry, 'onRead')) {
        $socket->close();
        return;
      }

      $this->answer($event->stream, 150, sprintf(
        'Opening %s mode data connection for %s (%d bytes)',
        $this->sessions[$event->stream->hashCode()]->getType(),
        $entry->getName(),
        $entry->getSize()
      ));
      try {
        $entry->open(SE_READ);
        while (!$socket->eof() && $buf= $entry->read()) {
          if (!$socket->write($buf)) break;
        }
        $entry->close();
      } catch (XPException $e) {
        $this->answer($event->stream, 550, $params.': '.$e->getMessage());
      } finally(); {
        $socket->close();
        if ($e) return;
      }
      $this->answer($event->stream, 226, 'Transfer complete');
    }

    /**
     * Callback for the "STOR" command
     *
     * @param   peer.server.ConnectionEvent event
     * @param   string params
     */
    public function onStor($event, $params) {
      if (!$socket= $this->openDatasock($event)) return;
      
      if (!($entry= $this->storage->lookup($event->stream->hashCode(), $params))) {

        // Invoke interceptor
        $entry= $this->storage->createEntry($event->stream->hashCode(), $params, ST_ELEMENT);
        if (!$this->checkInterceptors($event, $entry, 'onCreate')) {
          $socket->close();
          return;
        }

        try {
          $entry= $this->storage->create($event->stream->hashCode(), $params, ST_ELEMENT);
        } catch (XPException $e) {
          $this->answer($event->stream, 550, $params.': '.$e->getMessage());
          $socket->close();
          return;
        }
      } else if ($entry instanceof StorageCollection) {
        $this->answer($event->stream, 550, $params.': is a directory');
        $socket->close();
        return;
      }
      
      $this->answer($event->stream, 150, sprintf(
        'Opening %s mode data connection for %s',
        $this->sessions[$event->stream->hashCode()]->getType(),
        $entry->getName()
      ));
      try {
        $entry->open(SE_WRITE);
        while (!$socket->eof() && $buf= $socket->readBinary(32768)) {
          $entry->write($buf);
        }
        $entry->close();
      } catch (XPException $e) {
        $this->answer($event->stream, 550, $params.': '.$e->getMessage());
      } finally(); {
        $socket->close();
        if ($e) return;
      }
      
      // Post check interception
      if (!$this->checkInterceptors($event, $entry, 'onStored')) {
        $entry->delete();
        return;
      }

      $this->answer($event->stream, 226, 'Transfer complete');
    }

    /**
     * Callback for the "DELE" command
     *
     * @param   peer.server.ConnectionEvent event
     * @param   string params
     */
    public function onDele($event, $params) {
      if (!($entry= $this->storage->lookup($event->stream->hashCode(), $params))) {
        $this->answer($event->stream, 550, $params.': No such file or directory');
        return;
      }
      if ($entry instanceof StorageCollection) {
        $this->answer($event->stream, 550, $params.': is a directory');
        return;
      }
      
      // Invoke interceptor
      if (!$this->checkInterceptors($event, $entry, 'onDelete')) return;

      try {
        $entry->delete();
      } catch (IOException $e) {
        $this->answer($event->stream, 450, $params.': ', $e->getMessage());
        return;
      }

      $this->answer($event->stream, 250, $params.': file deleted');
    }
    
    /**
     * Rename a file from filename
     *
     * @param   peer.server.ConnectionEvent event
     * @param   string params
     */
    public function onRnfr($event, $params) {
      if (!($entry= $this->storage->lookup($event->stream->hashCode(), $params))) {
        $this->answer($event->stream, 550, $params.': No such file or directory');
        return;
      }
      $this->cat && $this->cat->debug($entry);
      
      $this->sessions[$event->stream->hashCode()]->setTempVar('rnfr', $entry);
      $this->answer($event->stream, 350, 'File or directory exists, ready for destination name.');
    }
    
    /**
     * Rename a file into filename
     *
     * @param   peer.server.ConnectionEvent event
     * @param   string params
     */
    public function onRnto($event, $params) {
      if (!$entry= $this->sessions[$event->stream->hashCode()]->getTempVar('rnfr')) {
        $this->answer($event->stream, 503, 'Bad sequence of commands');
        return;
      }
      
      // Invoke interceptor
      if (!$this->checkInterceptors($event, $entry, 'onRename')) return;

      try {
        $entry->rename($params);
        $this->cat->debug($params);
      } catch (XPException $e) {
        $this->answer($event->stream, 550, $params.': '. $e->getMessage());
        return;
      }
      
      $this->sessions[$event->stream->hashCode()]->removeTempVar('rnfr');
      $this->answer($event->stream, 250, 'Rename successful');
    }
    

    /**
     * Callback for the "TYPE" command
     *
     * @param   peer.server.ConnectionEvent event
     * @param   string params
     */
    public function onType($event, $params) {
      switch ($params= strtoupper($params)) {
        case TYPE_ASCII:
        case TYPE_BINARY:
          $this->sessions[$event->stream->hashCode()]->setType($params);
          $this->answer($event->stream, 200, 'Type set to '.$params);
          break;
          
        default:
          $this->answer($event->stream, 550, 'Unknown type "'.$params.'"');
      }
    }

    /**
     * Callback for the "STRU" command
     *
     * @param   peer.server.ConnectionEvent event
     * @param   string params
     */
    public function onStru($event, $params) {
      switch ($params= strtoupper($params)) {
        case STRU_FILE:
          $this->answer($event->stream, 200, 'Structure set to '.$params);
          break;
        
        case STRU_RECORD:
        case STRU_PAGE:
          $this->answer($event->stream, 504, $params.': unsupported structure type');
          break;
          
        default:
          $this->answer($event->stream, 501, $params.': unrecognized structure type');
      }
    }

    /**
     * Callback for the "MODE" command
     *
     * @param   peer.server.ConnectionEvent event
     * @param   string params
     */
    public function onMode($event, $params) {
      switch ($params= strtoupper($params)) {
        case MODE_STREAM:
          $this->answer($event->stream, 200, 'Mode set to '.$params);
          break;
        
        case MODE_BLOCK:
        case STRU_COMPRESSED:
          $this->answer($event->stream, 504, $params.': unsupported transfer mode');
          break;
          
        default:
          $this->answer($event->stream, 501, $params.': unrecognized transfer mode');
      }
    }

    /**
     * Callback for the "QUIT" command
     *
     * @param   peer.server.ConnectionEvent event
     * @param   string params
     */
    public function onQuit($event, $params) {
      $this->answer($event->stream, 221, 'Goodbye');
      $event->stream->close();
      
      // Kill associated session
      delete($this->sessions[$event->stream->hashCode()]);
    }

    /**
     * Callback for the "PORT" command
     *
     * @param   peer.server.ConnectionEvent event
     * @param   string params
     */
    public function onPort($event, $params) {
      $this->mode[$event->stream->hashCode()]= DATA_ACTIVE;
      $octets= sscanf($params, '%d,%d,%d,%d,%d,%d');
      $host= sprintf('%s.%s.%s.%s', $octets[0], $octets[1], $octets[2], $octets[3]);
      $port= ($octets[4] * 256) + $octets[5];

      $this->cat && $this->cat->debug('+++ Host is ', $host);
      $this->cat && $this->cat->debug('+++ Port is ', $port);

      $this->datasock[$event->stream->hashCode()]= new BSDSocket($host, $port);
      $this->answer($event->stream, 200, 'PORT command successful');      
    }

    /**
     * Callback for the "OPTS" command
     *
     * @param   peer.server.ConnectionEvent event
     * @param   string params
     */
    public function onOpts($event, $params) {
      if (2 != sscanf($params, '%s %s', $option, $value)) {
        $this->answer($event->stream, 501, 'OPTS: Invalid numer of arguments');
        return;
      }
      
      // Do not recognize any opts
      $this->answer($event->stream, 501, 'Opts: '.$option.'not understood');
    }

    /**
     * Callback for the "PASV" command
     *
     * @param   peer.server.ConnectionEvent event
     * @param   string params
     */
    public function onPasv($event, $params) {
      $this->mode[$event->stream->hashCode()]= DATA_PASSIVE;

      if ($this->datasock[$event->stream->hashCode()]) {
        $port= $this->datasock[$event->stream->hashCode()]->port;   // Recycle it!
      } else {      
        $port= rand(1000, 65536);
        $this->datasock[$event->stream->hashCode()]= new ServerSocket($this->server->socket->host, $port);
        try {
          $this->datasock[$event->stream->hashCode()]->create();
          $this->datasock[$event->stream->hashCode()]->bind();
          $this->datasock[$event->stream->hashCode()]->listen();
        } catch (IOException $e) {
          $this->answer($event->stream, 425, 'Cannot open passive connection '.$e->getMessage());
          delete($this->datasock[$event->stream->hashCode()]);
          return;
        }
      }
      $this->cat && $this->cat->debug('Passive mode: Data socket is', $this->datasock[$event->stream->hashCode()]);
      $octets= strtr(gethostbyname($this->server->socket->host), '.', ',').','.($port >> 8).','.($port & 0xFF);
      $this->answer($event->stream, 227, 'Entering passive mode ('.$octets.')');
    }
    
    /**
     * Method to be triggered when a client connects
     *
     * @param   peer.server.ConnectionEvent event
     */
    public function connected($event) {
      $this->cat && $this->cat->debugf('===> Client %s connected', $event->stream->host);

      // Create a new session object for this client
      $this->sessions[$event->stream->hashCode()]= new FtpSession();
      $this->answer($event->stream, 220, 'FTP server ready');
    }
    
    /**
     * Method to be triggered when a client has sent data
     *
     * @param   peer.server.ConnectionEvent event
     */
    public function data($event) {
      static $public= array('onhelp', 'onuser', 'onpass', 'onquit');
      
      $this->cat && $this->cat->debug('>>> ', addcslashes($event->data, "\0..\17"));
      sscanf($event->data, "%s %[^\r]", $command, $params);
      $method= 'on'.strtolower($command);

      // Check if method is implemented and answer with code 550 in case
      // it isn't.
      if (!method_exists($this, $method)) {
        $this->answer($event->stream, 500, $command.' not understood');
        return;
      }
      
      // Check if user needs to be logged in in order to execute this command
      if (
        !$this->sessions[$event->stream->hashCode()]->isAuthenticated() && 
        !in_array($method, $public)
      ) {
        $this->answer($event->stream, 530, 'Please log in first');
        return;
      }
      
      try {
        $this->{$method}($event, $params);
      } catch (XPException $e) {
        $this->cat && $this->cat->warn('*** ', $e->toString());
        // Fall through
      }
      xp::gc();
    }
    
    /**
     * Method to be triggered when a client disconnects
     *
     * @param   peer.server.ConnectionEvent event
     */
    public function disconnected($event) {
      $this->cat && $this->cat->debugf('Client %s disconnected', $event->stream->host);
      
      // Kill associated session
      delete($this->sessions[$event->stream->hashCode()]);
    }

  } 
?>
