<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.server.ConnectionListener');
  uses('util.cmd.Console');

  /**
   * Implement FTP server functionality
   *
   * @see      xp://peer.server.ConnectionListener
   * @purpose  Connection listener
   */
  class FtpConnectionListener extends ConnectionListener {
    var
      $user             = array('username' => NULL, 'loggedin' => FALSE),
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
        $answer= $code.'-'.$text.":\r\n  ".implode("\r\n  ", $lines)."\r\n".$code." End\r\n";
      } else {
        $answer= sprintf("%d %s\r\n", $code, $text);
      }
      Console::writeLine('<<< ', addcslashes($answer, "\0..\17"));
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
        $this->answer($event->stream, 550, $e->getMessage());
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
        if (0 != strncmp('on', $name, 2)) continue;

        if ($i++ % 8 == 0) $methods[++$offset]= '';
        $methods[$offset].= str_pad(strtoupper(substr($name, 2)), 8);
      }
      $this->answer($event->stream, 214, 'The following commands are recognized', $methods);
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
     * LIST: This command causes a list of file names and file details 
     * to be sent from the FTP site to the client.
     *
     * @access  protected
     * @param   &peer.server.ConnectionEvent event
     * @param   string params
     */
    function onList(&$event, $params) {
      if (!($entry= &$this->storage->lookup($params))) {
        $this->answer($event->stream, 550, $params.': No such file or directory');
        return;
      }
      
      // Assume this is a passive connection
      $m= &$this->datasock->accept();
      $this->answer($event->stream, 150, 'Opening ASCII mode data connection for filelist');
      
      // If a collection was specified, list its elements, otherwise,
      // list the single element
      if (is('StorageCollection', $entry)) {
        foreach ($entry->elements() as $element) {
          $m->write($element->longRepresentation()."\r\n");
        }
      } else {
        $m->write($entry->longRepresentation()."\r\n");
      }
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
      $this->answer($event->stream, 150, 'Opening ASCII mode data connection for filelist');
      
      // If a collection was specified, list its elements, otherwise,
      // list the single element
      if (is('StorageCollection', $entry)) {
        foreach ($entry->elements() as $element) {
          $m->write($element->getName()."\r\n");
        }
      } else {
        $m->write($entry->getName()."\r\n");
      }
      $this->answer($event->stream, 226, 'Transfer complete');
    }

    /**
     * Callback for the "MDTM" command
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
      Console::writeLine($entry->toString());
      if (is('StorageCollection', $entry)) {
        $this->answer($event->stream, 550, $params.': is a directory');
        return;
      }
      
      // Assume this is a passive connection
      $m= &$this->datasock->accept();
      $this->answer($event->stream, 150, sprintf(
        'Opening BINARY mode data connection for %s (%d bytes)',
        $entry->getName(),
        $entry->getSize()
      ));
      try(); {
        $entry->open(SE_READ);
        while ($buf= $entry->read()) {
          $m->write($buf);
        }
        $entry->close();
      } if (catch('Exception', $e)) {
        $this->answer($event->stream, 550, $params.': '.$e->getMessage());
        return;
      }
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
        'Opening BINARY mode data connection for %s',
        $entry->getName()
      ));
      try(); {
        $entry->open(STOR_WRITE);
        while ($buf= $m->read()) {
          $entry->write($buf);
        }
        $entry->close();
      } if (catch('Exception', $e)) {
        $this->answer($event->stream, 550, $params.': '.$e->getMessage());
        return;
      }
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
    
      // TBD: Actually do something with type
      $this->answer($event->stream, 200, 'Type set to '.$params);
      
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
      Console::writeLine('+++ Port is ', $port);
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
      var_dump($this->datasock);

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
      Console::writeLinef('===> Client %s connected', $event->stream->host);
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

      Console::writeLine('>>> ', addcslashes($event->data, "\0..\17"));
      $r= sscanf($event->data, "%s %[^\r]", $command, $params);
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
      
      $this->{$method}($event, $params);
    }
    
    /**
     * Method to be triggered when a client disconnects
     *
     * @access  public
     * @param   &peer.server.ConnectionEvent event
     */
    function disconnected(&$event) {
      Console::writeLinef('===> Client %s disconnected', $event->stream->host);
    }
  }
?>
