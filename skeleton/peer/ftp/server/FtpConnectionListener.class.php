<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.server.ConnectionListener');
  
  define('TYPE_ASCII',  'A');
  define('TYPE_BINARY', 'I');

  /**
   * Implement FTP server functionality
   *
   * @see      http://ipswitch.com/Support/WS_FTP-Server/guide/v4/A_FTPref4.html 
   * @see      xp://peer.server.ConnectionListener
   * @purpose  Connection listener
   */
  class FtpConnectionListener extends ConnectionListener {
    var
      $user             = array('username' => NULL, 'loggedin' => FALSE),
      $type             = TYPE_ASCII,
      $cat              = NULL,
      $authenticator    = NULL,
      $storage          = NULL,
      $datasock         = NULL;   // For passive mode

    /**
     * Constructor
     *
     * @access  public
     * @param   &peer.ftp.server.Storage storage
     * @param   &peer.ftp.server.Authenticator authenticator
     */
    function __construct(&$storage, &$authenticator) {
      $this->storage= &$storage;
      $this->authenticator= &$authenticator;
    }

    /**
     * Set a trace for debugging
     *
     * @access  public
     * @param   &util.log.LogCategory cat
     */
    function setTrace(&$cat) { 
      $this->cat= &$cat;
    }

    /**
     * Returns end of line identifier depending on the type
     *
     * @access  protected
     * @return  string
     */
    function eol() {
      return (TYPE_ASCII == $this->type) ? "\r\n" : "\n";
    }
    
    /**
     * Returns type name depending on the type
     *
     * The following codes are assigned:
     * <pre>
     * A = ASCII (text files)
     * N = Non-print (files that have no vertical format controls such 
     *     as carriage returns and line feeds)
     * T = Telnet format effectors (files that have ASCII or EBCDIC 
     *     vertical format controls)
     * E = EBCDIC (files being transferred between systems that use 
     *     EBCDIC for internal character representation)
     * C = Carriage Control (ASA) (files that contain ASA [FORTRAN] 
     *     vertical format controls)
     * I = Image (binary files)
     * L = Local byte size (files that need to be transferred using 
     *     specific non-standard size bytes)
     * </pre>
     *
     * The default representation type is ASCII Non-print. This 
     * implementation supports ASCII (A) and BINARY (I)
     *
     * @access  protected
     * @return  string
     */
    function typeName() {
      static $names= array(
        TYPE_ASCII  => 'ASCII',
        TYPE_BINARY => 'BINARY'
      );
      return $names[$this->type];
    }
    
    /**
     * Write an answer message to the socket
     *
     * @access  protected
     * @param   &peer.Socket sock
     * @param   int code
     * @param   string text
     * @param   array lines default NULL lines of a multiline response
     * @return  int number of bytes written
     * @throws  io.IOException
     */
    function answer(&$sock, $code, $text, $lines= NULL) {
      if (is_array($lines)) {
        $answer= $code.'-'.$text.":\n  ".implode("\n  ", $lines)."\n".$code." End\n";
      } else {
        $answer= sprintf("%d %s\n", $code, $text);
      }
      $this->cat && $this->cat->debug('<<< ', addcslashes($answer, "\0..\17"));
      return $sock->write($answer);
    }
    
    /**
     * Callback for the "USER" command
     *
     * @access  protected
     * @param   &peer.server.ConnectionEvent event
     * @param   string params
     */
    function onUser(&$event, $params) {
      $this->user= array(
        'name'     => $params,
        'loggedin' => FALSE
      );
      $this->answer($event->stream, 331, 'Password required for '.$this->user['name']);
    }
    
    /**
     * Callback for the "PASS" command
     *
     * @access  protected
     * @param   &peer.server.ConnectionEvent event
     * @param   string params
     */
    function onPass(&$event, $params) {
      try(); {
        $r= $this->authenticator->authenticate($this->user['name'], $params);
      } if (catch('AuthenticatorException', $e)) {
        $this->answer($event->stream, 550, $e->getMessage());
        return;
      }
      
      // Did the authentication succeed?
      if (!$r) {
        $this->answer($event->stream, 530, 'Autentication failed for '.$this->user['name']);
        return;
      }
      $this->answer($event->stream, 230, 'User '.$this->username.' logged in');
      $this->user['loggedin']= TRUE;
    }
    
    /**
     * REIN: This command terminates a USER, flushing all I/O and 
     * account information, except to allow any transfer in progress 
     * to be completed. A USER command may be expected to follow.
     *
     * @access  protected
     * @param   &peer.server.ConnectionEvent event
     * @param   string params
     */
    function onRein(&$event, $params) {
      delete($this->datasock);
      $this->user['loggedin']= FALSE;
    }
        
    /**
     * Callback for the "PWD" command
     *
     * @access  protected
     * @param   &peer.server.ConnectionEvent event
     * @param   string params
     */
    function onPwd(&$event, $params) {
      $this->answer($event->stream, 200, '"'.$this->storage->getBase().'" is current directory');
    }

    /**
     * CWD: This command allows the user to work with a different 
     * directory or dataset without altering his login or account 
     * information.
     *
     * @access  protected
     * @param   &peer.server.ConnectionEvent event
     * @param   string params
     */
    function onCwd(&$event, $params) {
      try(); {
        $pwd= $this->storage->setBase($params);
      } if (catch('Exception', $e)) {
        $this->answer($event->stream, 450, $e->getMessage());
        return;
      }
      $this->answer($event->stream, 200, '"'.$pwd.'" is new working directory');
    }

    /**
     * FEAT: This command causes the FTP server to list all new FTP 
     * features that the server supports beyond those described in 
     * RFC 959.
     *
     * @access  protected
     * @param   &peer.server.ConnectionEvent event
     * @param   string params
     */
    function onFeat(&$event, $params) {
      $this->answer($event->stream, 211, 'Features', array('MDTM', 'SIZE'));
    }

    /**
     * HELP: This command causes the server to send a list of supported 
     * commands and other helpful information.
     *
     * @access  protected
     * @param   &peer.server.ConnectionEvent event
     * @param   string params
     */
    function onHelp(&$event, $params) {
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
     * @access  protected
     * @param   &peer.server.ConnectionEvent event
     * @param   string params
     */
    function onSite(&$event, $params) {
      $method= 'onSite'.strtolower(strtok($params, ' '));

      // Check if method is implemented and answer with code 550 in case
      // it isn't.
      if (!method_exists($this, $method)) {
        $this->answer($event->stream, 550, $command.' not understood');
        return;
      }

      $this->{$method}($event, substr($params, strlen($method) - 6));
    }
    
    /**
     * SITE HELP
     *
     * @access  protected
     * @param   &peer.server.ConnectionEvent event
     * @param   string params
     */
    function onSiteHelp(&$event, $params) {
      return $this->onHelp($event, $params);
    }

    /**
     * SITE CHMOD
     *
     * @access  protected
     * @param   &peer.server.ConnectionEvent event
     * @param   string params
     */
    function onSiteChmod(&$event, $params) {
      sscanf($params, '%d %s', $permissions, $uri);
      if (!($entry= &$this->storage->lookup($uri))) {
        $this->answer($event->stream, 550, $uri.': No such file or directory');
        return;
      }
      
      $entry->setPermissions($permissions);
      $this->answer($event->stream, 200, 'SITE CHMOD command successful');
    }
    
    /**
     * Callback for the "SYST" command
     *
     * @access  protected
     * @param   &peer.server.ConnectionEvent event
     * @param   string params
     */
    function onSyst(&$event, $params) {
      $this->answer($event->stream, 215, 'UNIX Type: L8');
    }

    /**
     * NOOP:  This command does not affect any parameters or previously 
     * entered commands. It specifies no action other than that the 
     * server send an OK reply.
     *
     * @access  protected
     * @param   &peer.server.ConnectionEvent event
     * @param   string params
     */    
    function onNoop(&$event, $params) {
      $this->answer($event->stream, 200, 'OK');
    }

    /**
     * Helper method
     *
     * @access  protected
     * @param   int bits
     * @return  string
     */
    function _rwx($bits) {
      return (
        (($bits & 4) ? 'r' : '-').
        (($bits & 2) ? 'w' : '-').
        (($bits & 1) ? 'x' : '-')
      );
    }
    
    /**
     * Create a string representation from integer permissions
     *
     * @access  protected
     * @param   int permissions
     * @return  string
     */
    function permissionString($permissions) {
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
     * @access  protected
     * @param   &peer.server.ConnectionEvent event
     * @param   string params
     */
    function onList(&$event, $params) {
      $params= str_replace('-L', '', $params);
      if (!($entry= &$this->storage->lookup($params))) {
        $this->answer($event->stream, 550, $params.': No such file or directory');
        return;
      }
      
      // Assume this is a passive connection
      $m= &$this->datasock->accept();
      $this->answer($event->stream, 150, sprintf(
        'Opening %s mode data connection for filelist',
        $this->typeName()
      ));
      
      // If a collection was specified, list its elements, otherwise,
      // list the single element
      if (is('StorageCollection', $entry)) {
        $elements= $entry->elements();
      } else {
        $elements= array($entry);
      }
      
      for ($i= 0, $s= sizeof($elements); $i < $s; $i++) {
        $buf= sprintf(
          '%s  %2d %s  %s  %8d %s %s',
          $this->permissionString($elements[$i]->getPermissions()),
          $elements[$i]->numLinks(),
          $elements[$i]->getOwner(),
          $elements[$i]->getGroup(),
          $elements[$i]->getSize(),
          date('M d H:i', $elements[$i]->getModifiedStamp()),
          $elements[$i]->getName()
        );
        $this->cat && $this->cat->debug('    ', $buf);
        $m->write($buf.$this->eol());
      }
      $m->close();
      $this->answer($event->stream, 226, 'Transfer complete');
    }

    /**
     * NLST: This command causes a list of file names (with no other 
     * information) to be sent from the FTP site to the client.
     *
     * @access  protected
     * @param   &peer.server.ConnectionEvent event
     * @param   string params
     */
    function onNlst(&$event, $params) {
      if (!($entry= &$this->storage->lookup($params))) {
        $this->answer($event->stream, 550, $params.': No such file or directory');
        return;
      }
      
      // Assume this is a passive connection
      $m= &$this->datasock->accept();
      $this->answer($event->stream, 150, sprintf(
        'Opening %s mode data connection for filelist',
        $this->typeName()
      ));
      
      // If a collection was specified, list its elements, otherwise,
      // list the single element
      if (is('StorageCollection', $entry)) {
        $elements= $entry->elements();
      } else {
        $elements= array($entry);
      }
      
      for ($i= 0, $s= sizeof($elements); $i < $s; $i++) {
        $m->write($elements[$i]->getName().$this->eol());
      }
      $m->close();
      $this->answer($event->stream, 226, 'Transfer complete');
    }

    /**
     * MDTM: This command can be used to determine when a file in the 
     * server was last modified.
     *
     * @access  protected
     * @param   &peer.server.ConnectionEvent event
     * @param   string params
     */
    function onMdtm(&$event, $params) {
      if (!($entry= &$this->storage->lookup($params))) {
        $this->answer($event->stream, 550, $params.': No such file or directory');
        return;
      }
      
      $this->answer($event->stream, 213, date('YmdHis', $entry->getModifiedStamp()));
    }

    /**
     * SIZE:  This command is used to obtain the transfer size of a file 
     * from the server: that is, the exact number of octets (8 bit bytes) 
     * which would be transmitted over the data connection should that 
     * file be transmitted. 
     *
     * @access  protected
     * @param   &peer.server.ConnectionEvent event
     * @param   string params
     */
    function onSize(&$event, $params) {
      if (!($entry= &$this->storage->lookup($params))) {
        $this->answer($event->stream, 550, $params.': No such file or directory');
        return;
      }
      
      $this->answer($event->stream, 213, $entry->getSize());
    }

    /**
     * MKD:  This command causes the directory specified in pathname to 
     * be created as a directory (if pathname is absolute) or as a 
     * subdirectory of the current working directory (if pathname is 
     * relative).
     *
     * @access  protected
     * @param   &peer.server.ConnectionEvent event
     * @param   string params
     */
    function onMkd(&$event, $params) {
      if ($this->storage->lookup($params)) {
        $this->answer($event->stream, 550, $params.': already exists');
        return;
      }
      
      // Create the element
      try(); {
        $this->storage->create($params, ST_COLLECTION);
      } if (catch('Exception', $e)) {
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
     * @access  protected
     * @param   &peer.server.ConnectionEvent event
     * @param   string params
     */
    function onRmd(&$event, $params) {
      if (!($element= &$this->storage->lookup($params))) {
        $this->answer($event->stream, 550, $params.': no such file or directory');
        return;
      }
      
      // Delete the element
      try(); {
        $element->delete();
      } if (catch('Exception', $e)) {
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
     * @access  protected
     * @param   &peer.server.ConnectionEvent event
     * @param   string params
     */
    function onRetr(&$event, $params) {
      if (!($entry= &$this->storage->lookup($params))) {
        $this->answer($event->stream, 550, $params.': No such file or directory');
        return;
      }
      $this->cat && $this->cat->debug($entry->toString());
      if (is('StorageCollection', $entry)) {
        $this->answer($event->stream, 550, $params.': is a directory');
        return;
      }
      
      // Assume this is a passive connection
      $m= &$this->datasock->accept();
      $this->answer($event->stream, 150, sprintf(
        'Opening %s mode data connection for %s (%d bytes)',
        $this->typeName(),
        $entry->getName(),
        $entry->getSize()
      ));
      try(); {
        $entry->open(SE_READ);
        while (!$m->eof() && $buf= $entry->read()) {
          $m->write($buf);
        }
        $entry->close();
      } if (catch('Exception', $e)) {
        $this->answer($event->stream, 550, $params.': '.$e->getMessage());
        return;
      }
      $m->close();
      $this->answer($event->stream, 226, 'Transfer complete');
    }

    /**
     * Callback for the "STOR" command
     *
     * @access  protected
     * @param   &peer.server.ConnectionEvent event
     * @param   string params
     */
    function onStor(&$event, $params) {
      if (!($entry= &$this->storage->lookup($params))) {
        try(); {
          $entry= &$this->storage->create($params, ST_ELEMENT);
        } if (catch('Exception', $e)) {
          $this->answer($event->stream, 550, $params.': '.$e->getMessage());
          return;
        }
      } else if (is('StorageCollection', $entry)) {
        $this->answer($event->stream, 550, $params.': is a directory');
        return;
      }
      
      // Assume this is a passive connection
      $m= &$this->datasock->accept();
      $this->answer($event->stream, 150, sprintf(
        'Opening %s mode data connection for %s',
        $this->typeName(),
        $entry->getName()
      ));
      try(); {
        $entry->open(SE_WRITE);
        while (!$m->eof() && $buf= $m->readBinary()) {
          $entry->write($buf);
        }
        $entry->close();
      } if (catch('Exception', $e)) {
        $this->answer($event->stream, 550, $params.': '.$e->getMessage());
        return;
      }
      $m->close();
      $this->answer($event->stream, 226, 'Transfer complete');
    }

    /**
     * Callback for the "TYPE" command
     *
     * @access  protected
     * @param   &peer.server.ConnectionEvent event
     * @param   string params
     */
    function onType(&$event, $params) {
      switch ($params) {
        case TYPE_ASCII:
        case TYPE_BINARY:
          $this->type= $params;
          $this->answer($event->stream, 200, 'Type set to '.$params);
          break;
          
        default:
          $this->answer($event->stream, 550, 'Unknown type "'.$params.'"');
      }
    }

    /**
     * Callback for the "QUIT" command
     *
     * @access  protected
     * @param   &peer.server.ConnectionEvent event
     * @param   string params
     */
    function onQuit(&$event, $params) {
      $this->answer($event->stream, 221, 'Goodbye');
      $event->stream->close();
    }

    /**
     * Callback for the "PORT" command
     *
     * @access  protected
     * @param   &peer.server.ConnectionEvent event
     * @param   string params
     */
    function onPort(&$event, $params) {
      $octets= sscanf($params, '%d,%d,%d,%d,%d,%d');
      $port= ($octets[5] * 256) + $octets[6];
      $this->cat && $this->cat->debug('+++ Port is ', $port);
      $this->answer($event->stream, 200, 'PORT command successful');

      // TBI: What next?
      var_dump($this->datasock);
    }

    /**
     * Callback for the "OPTS" command
     *
     * @access  protected
     * @param   &peer.server.ConnectionEvent event
     * @param   string params
     */
    function onOpts(&$event, $params) {
      if (2 != sscanf($params, '%s %s', $option, $value)) {
        $this->answer($event->stream, 501, 'OPTS: Invalid numer of arguments');
        return;
      }
      
      // TBI: Do something about it. For now, answer with a lie!
      $this->answer($event->stream, 200, 'Option '.$option.' set to '.$value);
    }

    /**
     * Callback for the "PASV" command
     *
     * @access  protected
     * @param   &peer.server.ConnectionEvent event
     * @param   string params
     */
    function onPasv(&$event, $params) {
      if ($this->datasock) {
        $port= $this->datasock->port;   // Recycle it!
      } else {
        $port= rand(1000, 65536);
        $this->datasock= &new ServerSocket('ftp.banane.i.schlund.de', $port);
        try(); {
          $this->datasock->create();
          $this->datasock->bind();
          $this->datasock->listen();
        } if (catch('IOException', $e)) {
          $this->answer($event->stream, 425, 'Cannot open passive connection '.$e->getMessage());
          delete($this->datasock);
          return;
        }
      }
      $this->cat && $this->cat->debug('Passive mode: Data socket is', $this->datasock);

      $octets= strtr('172.17.29.15', '.', ',').','.($port >> 8).','.($port & 0xFF);
      $this->answer($event->stream, 227, 'Entering passive mode ('.$octets.')');
    }
    
    /**
     * Method to be triggered when a client connects
     *
     * @access  public
     * @param   &peer.server.ConnectionEvent event
     */
    function connected(&$event) {
      $this->cat && $this->cat->debugf('===> Client %s connected', $event->stream->host);
      $this->answer($event->stream, 220, 'FTP server ready');
    }
    
    /**
     * Method to be triggered when a client has sent data
     *
     * @access  public
     * @param   &peer.server.ConnectionEvent event
     */
    function data(&$event) {
      static $public= array('onhelp', 'onuser', 'onpass', 'onquit');

      $this->cat && $this->cat->debug('>>> ', addcslashes($event->data, "\0..\17"));
      sscanf($event->data, "%s %[^\r]", $command, $params);
      $method= 'on'.strtolower($command);

      // Check if method is implemented and answer with code 550 in case
      // it isn't.
      if (!method_exists($this, $method)) {
        $this->answer($event->stream, 550, $command.' not understood');
        return;
      }
      
      // Check if user needs to be logged in in order to execute this command
      if (!$this->user['loggedin'] && !in_array($method, $public)) {
        $this->answer($event->stream, 530, 'Please log in first');
        return;
      }
      
      try(); {
        $this->{$method}($event, $params);
      } if (catch('Exception', $e)) {
        $this->cat && $this->cat->warn('*** ', $e->toString());
        // Fall through
      }
      xp::gc();
    }
    
    /**
     * Method to be triggered when a client disconnects
     *
     * @access  public
     * @param   &peer.server.ConnectionEvent event
     */
    function disconnected(&$event) {
      $this->cat && $this->cat->debugf('Client %s disconnected', $event->stream->host);
    }

  } implements(__FILE__, 'util.log.Traceable');
?>
