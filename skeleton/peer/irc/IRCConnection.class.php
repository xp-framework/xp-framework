<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'peer.Socket',
    'peer.irc.IRCConstants',
    'peer.irc.IRCUser',
    'peer.irc.IRCConnectionListener',
    'util.log.Traceable'
  );

  /**
   * IRC Connection
   *
   * Usage example:
   * <code>
   *   uses(
   *     'peer.irc.IRCConnection', 
   *     'util.log.Logger',
   *     'util.log.ConsoleAppender',
   *     'KrokerdilBotListener'
   *   );
   *   
   *   $c= new IRCConnection(new IRCUser('KrokerdilBot'), 'irc.xxx.net');
   *   
   *   $cat= Logger::getInstance()->getCategory();
   *   $cat->addAppender(new ConsoleAppender());
   *   $c->setTrace($cat);
   *   
   *   $c->addListener(new KrokerdilBotListener());
   *   try {
   *     $c->open();
   *     $c->run();
   *     $c->close();
   *   } catch (XPException $e) {
   *     $e->printStackTrace();
   *   }
   * </code>
   *
   * @see      xp://peer.irc.IRCConstants
   * @see      xp://peer.irc.IRCUser
   * @purpose  A socket connection to a RFC-1459 compatible IRC server.
   */
  class IRCConnection extends Object implements Traceable {
    public
      $sock         = NULL,
      $cat          = NULL,
      $user         = NULL,
      $listeners    = array();
      
    /**
     * Constructor
     *
     * @param   peer.irc.IRCUser user
     * @param   string server
     * @param   int port default 6667
     */
    public function __construct($user, $server, $port= 6667) {
      $this->user= $user;
      $this->sock= new Socket($server, $port);
    }
    
    /**
     * Adds a listener
     *
     * @see     xp://peer.irc.IRCConnectionListener
     * @param   peer.irc.IRCConnectionListener listener
     * @return  peer.irc.IRCConnectionListener the listener added
     */
    public function addListener($listener) {
      $this->listeners[]= $listener;
      return $listener;
    }
    
    /**
     * Set a logger category for debugging
     *
     * @param   util.log.LogCategory cat
     */
    public function setTrace($cat) {
      $this->cat= $cat;
    }
  
    /**
     * Opens connection to the IRC server. Also sets the socket to 
     * blocking mode.
     *
     * @see     xp://peer.Socket#connect
     * @see     xp://peer.Socket#setBlocking
     * @throws  peer.SocketException in case connecting fails
     */
    public function open() {
      if (!$this->sock->connect()) return FALSE;
      $this->sock->setBlocking(TRUE);
      $this->notify('connect', $this->sock->host, $this->sock->port); 
    }
    
    /**
     * Notify all listeners
     *
     * @param   string event
     * @param   mixed* parameters
     */
    protected function notify() {
      $a= func_get_args();
      $function= 'on'.ucfirst(array_shift($a));
      array_unshift($a, $this);
      for ($i= 0, $s= sizeof($this->listeners); $i < $s; $i++) {
        $this->cat && $this->cat->debugf(
          'Calling %s::%s(<self>, %s)', 
          $this->listeners[$i]->getClassName(), 
          $function, 
          var_export(array_slice($a, 1), 1)
        );
        call_user_func_array(array($this->listeners[$i], $function), $a);
      }
    }
    
    /**
     * Process a single line of read data
     *
     * @param   string r the data
     * @return  bool TRUE when line was successfully processed
     */
    public function process($r) { 
      if (':' == $r{0}) {
      
        if (4 == sscanf($r, ":%s %d %s %[^\r]", $server, $code, $target, $data)) {

          // Server messages
          // :irc.xxx.net 372 KrokerdilBot :- Excuse of the hour:
          // :irc.xxx.net 353 KrokerdilBot = #test :KrokerdilBot @krokerdil
          // :irc.xxx.net 366 KrokerdilBot #test :End of /NAMES list.
          // :irc.xxx.net 376 KrokerdilBot :End of /MOTD command.
          $this->cat && $this->cat->debugf('    Server message #%d %s', $code, IRCConstants::nameOf($code));
          
          switch ($code) {
            case IRC_REPLY_MOTDSTART:
            case IRC_REPLY_MOTD:
            case IRC_REPLY_ENDOFMOTD:
              $this->notify(substr(IRCConstants::nameOf($code), 6), $server, $target, $data);
              break;
            
            default:
              $this->notify('serverMessage', $server, $code, $target, $data);
          }
        } else if (4 <= sscanf($r, ":%[^!]!%s %s %s %[^\r]", $nick, $user, $action, $target, $data)) {

          // Other messages
          switch (strtolower($action)) {

            // :krokerdil!thekid@xxx.de PRIVMSG KrokerdilBot :Wop
            // :krokerdil!thekid@xxx.de PRIVMSG #test :Poff
            // :krokerdil!thekid@xxx.de PRIVMSG #test :\001ACTION thinks Bot is stoopid\001
            // :krokerdil!thekid@xxx.de PRIVMSG Bot :\001USERINFO\001
            // :krokerdil!thekid@xxx.de PRIVMSG KrokerdilBot :Hello\0033Hello
            case 'privmsg':
              if ("\1" == $data{1}) {
                list($command, $params)= explode(' ', substr($data, 2, -1), 2);
                $this->notify($command, $nick, $target, $params);
              } else {
                $this->notify('privateMessage', $nick, $target, substr($data, 1));
              }
              break;

            // :krokerdil!thekid@xxx.de JOIN :#test
            case 'join':
              $this->notify('joins', substr($target, 1), $nick);
              break;

            // :krokerdil!thekid@xxx.de PART #test :Leaving
            case 'part':
              $this->notify('parts', $target, $nick, substr($data, 1));
              break;

            // :krokerdil!thekid@xxx.de MODE #test +v KrokerdilBot
            // :krokerdil!thekid@xxx.de MODE #test +k HALLO
            // :krokerdil!thekid@xxx.de MODE #test -k HALLO
            // :krokerdil!thekid@xxx.de MODE #test +b *!*thekid@*.xxx.de
            // :krokerdil!thekid@xxx.de MODE #test +o KrokerdilBot 
            case 'mode':
              list($mode, $params)= explode(' ', $data, 2);
              $this->notify('modeChanges', $nick, $target, $mode, $params);
              break;

            // :krokerdil!thekid@xxx.de KICK #schlund Bot :IHATEYOU
            case 'kick':
              list($who, $reason)= explode(' ', $data, 2);
              $this->notify('kicks', $target, $nick, $who, substr($reason, 1));
              break;

            // :krokerdil!thekid@xxx.de QUIT :signoff: Ave
            case 'quit':
              $this->notify('quits', $target, $nick, $data);
              break;            

            // :krokerdil!thekid@xxx.de NICK :schnutepanzerdil
            case 'nick':
              $this->notify('nickChanges', $nick, substr($target, 1));
              break;
            
            // :krokerdil!thekid@xxx.de INVITE KrokerdilBot :#test
            case 'invite':
              $this->notify('invite', $nick, $target, substr($data, 1));
              break;
            
            // :krokerdil!thekid@xxx.de NOTICE #test :8tung baby
            // :krokerdil!thekid@xxx.de NOTICE KrokerdilBot :8tung baby
            case 'notice':
              $this->notify('notice', $nick, $target, substr($data, 1));
              break;
              
            // :krokerdil!thekid@xxx.de TOPIC #test :Korkerdl             
            case 'topic':
              $this->notify('topic', $nick, $target, substr($data, 1));
              break;

            default:
              $this->cat && $this->cat->warn('Unrecognized action', $action, '(', $nick, $user, $action, $target, $data, ')');
              return FALSE;
          }
        } else {
          $this->cat && $this->cat->error('Could not parse', $r);
          return FALSE;
        }
      } else {
        $this->cat && $this->cat->error('Malformed message', $r);
        return FALSE;
      }
      return TRUE;
    }
    
    /**
     * Set nickname and update this user's nickname.
     *
     * @param   string nick
     * @return  bool success TRUE in case the command could be sent
     * @throws  lang.IllegalStateException in case the socket is not connected
     */
    public function setNick($nick) {
      if (!$this->sock->isConnected()) {
        throw(new IllegalStateException('Not connected'));
      }
      if (!$this->writeln('NICK %s', $nick)) return FALSE;
      $this->user->setNick($nick);
      return TRUE;
    }
    
    /**
     * Join a channel
     *
     * @param   string channel without leading #
     * @param   string keyword default NULL
     * @return  bool success TRUE in case the command could be sent
     * @throws  lang.IllegalStateException in case the socket is not connected
     */
    public function join($channel, $keyword= NULL) {
      if (!$this->sock->isConnected()) {
        throw(new IllegalStateException('Not connected'));
      }
      
      // Be tolerant about channel parameter and rip off leading # if necessary
      return $this->writeln('JOIN #%s %s', ltrim($channel, '#'), $keyword);
    }
    
    /**
     * Leave a channel
     *
     * @param   string channel without leading #
     * @return  bool success TRUE in case the command could be sent
     * @throws  lang.IllegalStateException in case the socket is not connected
     */
    public function part($channel) {
      if (!$this->sock->isConnected()) {
        throw(new IllegalStateException('Not connected'));
      }
      
      // Be tolerant about channel parameter and rip off leading # if necessary
      return $this->writeln('PART #%s', ltrim($channel, '#'));
    }

    /**
     * Wrapper around writeln() that sends a PRIVMSG
     *
     * @param   string dest either a nick or a channel (beginning with "#")
     * @param   string format
     * @param   mixed* format_arguments
     * @return  bool success TRUE in case the command could be sent
     */
    public function sendMessage() {
      $dest= array_shift($a= func_get_args());
      return $this->writeln('PRIVMSG %s :%s', $dest, vsprintf(array_shift($a), $a));
    }

    /**
     * Wrapper around writeln() that sends a PRIVMSG with an ACTION
     *
     * @param   string dest either a nick or a channel (beginning with "#")
     * @param   string format
     * @param   mixed* format_arguments
     * @return  bool success TRUE in case the command could be sent
     */
    public function sendAction() {
      $dest= array_shift($a= func_get_args());
      return $this->writeln("PRIVMSG %s :\1ACTION %s\1", $dest, vsprintf(array_shift($a), $a));
    }
    
    /**
     * The main loop in which the socket is polled for data, notifying
     * the attached listeners of events.
     *
     * @throws  lang.IllegalStateException in case the socket is not connected
     * @throws  io.IOException in case registration failed
     */
    public function run() {
      if (!$this->sock->isConnected()) {
        throw(new IllegalStateException('Not connected'));
      }
      
      // Register with the server
      try {
        $this->writeln(
          'USER %s %s %s :%s', 
          $this->user->getUsername(),
          $this->user->getHostname(),
          $this->sock->host,
          $this->user->getRealname()
        );

        // Set nickname
        $this->setNick($this->user->getNick());
      } catch (IOException $e) {
        throw($e);
      }
      
      // Loop while socket is not disconnected
      $messages= array();
      while (!$this->sock->eof()) {
        if (!$r= $this->readln()) continue;
        
        // Reply to PINGs immediately
        if ('PING :' == substr($r, 0, 6)) {
          $this->writeln('PONG %s', substr($r, 6));
          $this->notify('pings', substr($r, 6));
          continue;
        }

        // Process data
        $this->process($r);
      }
    }
    
    /**
     * Close the communication socket. Issues a QUIT before 
     *
     * @see     xp://peer.Socket#close
     * @return  bool success
     */
    public function close() {
      if ($this->sock->isConnected()) return TRUE;
      
      $this->notify('disconnect', $this->sock->host, $this->sock->port); 
      
      // Politely say goodbye
      $this->writeln('QUIT');
      return $this->sock->close();
    }

    /**
     * Directly read a line off the socket. The returned line has any 
     * trailing CRLF trimmed off.
     *
     * @see     xp://peer.Socket#readLine
     * @return  string or FALSE to indicate failure
     */    
    public function readln() {
      xp::gc();
      try {
        $r= $this->sock->readLine(0x2000);
      } catch (IOException $e) {
        $this->cat && $this->cat->warn($e);
        return FALSE;
      }
      $this->cat && $this->cat->infof(
        '<<< %s %3d: %s', 
        gettype($r), 
        strlen($r), 
        addcslashes($r, "\0..\37")
      );
      
      return $r;
    }
    
    /**
     * Directly write a string to the socket. A CRLF is added automatically 
     * at the string's end.
     *
     * Note: It seems as if 448 bytes is the maximum length of a single
     * string that can be written e.g. as a PRIVMSG (including the PRIVMSG
     * command and destination). This limit is not checked upon.
     *
     * @see     xp://peer.Socket#write
     * @param   string format
     * @param   mixed* formatargs
     * @return  bool success
     */
    public function writeln() {
      $cmd= vsprintf(array_shift($a= func_get_args()), $a);
      $this->cat && $this->cat->infof(
        '>>> %s %3d: %s', 
        gettype($cmd), 
        strlen($cmd), 
        addcslashes($cmd, "\0..\37")
      );
      
      return $this->sock->write($cmd."\r\n");
    }
  } 
?>
