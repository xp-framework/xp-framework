<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
 uses('peer.BSDSocket');

 // Message Types
 define('IRC_MSGT_SERVER',     0x0000);   
 define('IRC_MSGT_CLIENT',     0x0001);   
 define('IRC_MSGT_USER',       0x0002);   
 
 // MOTD (Message Of The Day)
 define('IRC_MSGC_MOTD_BEGIN', 375);
 define('IRC_MSGC_MOTD',       372);
 define('IRC_MSGC_MOTD_END',   376);
 
 // LIST
 define('IRC_MSGC_LIST_BEGIN', 321);
 define('IRC_MSGC_LIST',       322);
 define('IRC_MSGC_LIST_END',   323);
 
 // Join replies
 define('IRC_MSGC_NAMES',      353);
 define('IRC_MSGC_NAMES_END',  366);
 
 /**
  * IRC (Internet Relay Chat) client implementation w/o the need for
  * any external libraries
  *
  * @see 	http://www.faqs.org/rfcs/rfc1459.html
  * @see 	http://www.irchelp.org/irchelp/rfc/rfc2812.txt
  */
  class IRCClient extends Object {
    var 
      $nick,
      $username,
      $realname;
      
    var
      $port	= 6667,
      $server	= 'localhost',
      $hostname	= 'localhost';
      
    var
      $_sock	= NULL,
      $_message = array();
      
    /**
     * Constructor
     *
     * @access  public
     * @param   array params
     */
    function __construct($params= NULL) {
      parent::__construct($params);
      
      // Set some defaults
      if (empty($this->username)) $this->username= get_current_user();
      if (empty($this->nick)) $this->nick= $this->username;

      // Create a socket object
      $this->_sock= &new BSDSocket(array(
        'host'	=> $this->server,
        'port'	=> $this->port
      ));
    }
    
    /**
     * Connect to the IRC server
     *
     * @access  public
     * @return  bool success
     */
    function connect() {
      return $this->_sock->connect();
    }
    
    /**
     * Fetch last message
     *
     * @access  public
     * @return  string message
     */
    function getLastMessage() {
      return $this->_message;
    }
    
    /**
     * Register ourselves
     *
     * @access  public
     * @param   string pass default NULL
     * @return  string motd
     * @throws  IllegalStateException if not connected
     */
    function register($pass= NULL) {
      if (!$this->_sock->isConnected()) {
        return throw(new IllegalStateException('Not connected'));
      }
      $ret= TRUE;
      
      // Send user information
      if (FALSE === ($this->_cmd(
        'USER %s %s %s :%s',
        $this->username,
        $this->hostname,
        $this->server,
        $this->realname
      ))) return FALSE;
      
      // Set nick
      if (FALSE === ($msg= $this->_cmd('NICK %s', $this->nick))) {
        return FALSE;
      }
      
      // Let's get the welcome message
      $data= FALSE;
      $motd= '';
      for ($i= 0, $s= sizeof($msg); $i < $s; $i++) {
        switch ((int)$msg[$i]['code']) {
          case IRC_MSGC_MOTD_BEGIN:
            $data= TRUE;
            break;
            
          case IRC_MSGC_MOTD_END:
            $data= FALSE;
            break;
            
          case IRC_MSGC_MOTD:
            if ($data) $motd.= substr($msg[$i]['data'], 1)."\n";
            break;
        }
      }
      
      // In case of success, we return the welcome message 
      return $motd;
    }
    
    /**
     * Get list of available channels
     *
     * Raw data looks like this:
     * <pre>
     *   :irc.server.com 321 nick Channel :Users  Name
     *   :irc.server.com 322 nick #support 7 :
     *   :irc.server.com 322 nick #tech 12 :Technical discussions
     *   :irc.server.com 323 nick :End of /LIST
     * </pre>
     *
     * Return value for given raw data:
     * <pre>
     *   array(2) {
     *     "#support" => array(2) {
     *       "users" => int(7),
     *       "name"  => bool(false)
     *     }
     *     "#tech" => array(2) {
     *       "users" => int(12),
     *       "name"  => "Technical discussions"
     *     }
     *  }
     * </pre>
     *
     * @access  public
     * @return  array channels Associative array of channels
     * @throws  FormatException in case of invalid return codes
     */
    function listChannels() {
      if (FALSE === ($list= $this->_cmd('LIST'))) return FALSE;
      
      $channels= array();
      for ($i= 0, $s= sizeof($list); $i < $s; $i++) {
      
        // Discard everything except for server messages
        if (IRC_MSGT_SERVER != $list[$i]['type']) continue;
        
        switch ((int)$list[$i]['code']) {
          case IRC_MSGC_LIST_BEGIN:
            break;
            
          case IRC_MSGC_LIST_END:
            break 2;
            
          case IRC_MSGC_LIST:
            // #schlund 3 :Schlund
            list($channel, $users, $name)= explode(' ', $list[$i]['data'], 3);
            $channels[$channel]= array(
              'users' => (int)$users,
              'name'  => substr($name, 1)
            );
            break;
            
          default:
            // Woops? What's this
            return throw(new FormatException(sprintf(
              'Illegal code %s found in LIST response',
              $list[$i]['code']
            )));
        }
      }
      
      // We've got the list of channels available
      return $channels;
    }
    
    /**
     * Join a channel
     *
     * Raw data looks like this:
     * <pre>
     *   :nick!user@remote JOIN :#channel
     *   :irc.server.com 353 nick = #channel :nick Samurai Poerl vicious
     *   :irc.server.com 366 nick #channel :End of /NAMES list.
     * </pre>
     *
     * @access  public
     * @param   string channel channel name
     * @param	string key default NULL key (password)
     * @return  array users
     */
    function join($channel, $key= NULL) {
      if (FALSE === ($msg= $this->_cmd('JOIN %s%s', $channel, (NULL == $key) ? '' : ' '.$key))) {
        return FALSE;
      }
      
      // Parse response
      $users= array();
      $joined= FALSE;
      for ($i= 0, $s= sizeof($msg); $i < $s; $i++) {
        if (
          ('JOIN' == $msg[$i]['code']) &&
          ($this->nick == $msg[$i]['nick'])
        ) $joined= TRUE;
        
        switch ((int)$msg[$i]['code']) {
          case IRC_MSGC_NAMES:
            // = #idev :timm|home thekid|ho @alex 
            $users= explode(' ', trim(substr($msg[$i]['data'], strlen($channel)+ 4)));
            break;
            
          case IRC_MSGC_NAMES_END:
            break 2;
        }
      }
      
      // Join failed (key wrong, e.g.)
      if (!$joined) {
        return throw(new Exception('JOIN '.$channel.' failed: '.implode("\n", $this->_message)));
      }
      
      return $users;
    }
    
    /**
     * Gets names of users per channel
     *
     * @access  public
     * @param   mixed channel default '' either a string defining a channel
     *          or an array defining a list of channels
     * @return  array first dimension: channels, second: users
     */
    function names($channel= '') {
      if (is_array($channel)) $channel= implode(',', $channel);
      if (FALSE === ($msg= $this->_cmd(trim(sprintf('NAMES %s', $channel))))) {
        return FALSE;
      }
    
      $users= array();
      for ($i= 0, $s= sizeof($msg); $i < $s; $i++) {
        switch ((int)$msg[$i]['code']) {
          case IRC_MSGC_NAMES:
            // = #idev :timm|home thekid|ho @alex 
            list($channel, $userlist)= explode(' :', trim(substr($msg[$i]['data'], 2)));
            $users[$channel]= explode(' ', $userlist);
            break;
            
          case IRC_MSGC_NAMES_END:
            return $users;
            
          default:
            // Woops? What's this
            return throw(new FormatException(sprintf(
              'Illegal code %s found in NAMES response',
              $list[$i]['code']
            )));
        }
      }
      
      return FALSE;
    }
    
    /**
     * Mark as away or back again
     *
     * @access  public
     * @param   mixed reason (reason for mark away, FALSE for mark back again)
     * @return  bool success
     */
    function markAway($reason) {
      if (FALSE === ($msg= $this->_cmd('AWAY%s', (FALSE === $reason ? '' : ' '.$reason)))) {
        return FALSE;
      }
      
      return $msg;
    }
    
    /**
     * Part (leave) a channel
     *
     * Raw data looks like this:
     * <pre>
     *   :nick!user@remote PART #channel
     * </pre>
     *
     * @access  public
     * @param   string channel The channel to part
     * @return  bool success
     */
    function part($channel) {
      if (FALSE === ($msg= $this->_cmd('PART %s', $channel))) {
        return FALSE;
      }
      
      // Parse response
      if (!(
        ($msg[0]['type'] == IRC_MSGT_CLIENT) &&
        ($msg[0]['code'] == 'PART')
      )) return FALSE;

      return TRUE;
    }
    
    /**
     * Send a message
     *
     * @access  public
     * @param   string message
     * @param	string recipient The recipient, either a nick or a #channel
     * @return  bool success
     */
    function privmsg($message, $recipient) {
      if (FALSE === ($msg= $this->_cmd('PRIVMSG %s :%s', $recipient, $message))) {
        return FALSE;
      }
      printf("privmsg(%s, %s):= %s\n", $message, $recipient, var_export($msg, 1));
      return TRUE;
    }
    
    /**
     * Check for new messages
     *
     * @access  
     * @param   
     * @return  
     */
    function recvmsg($timeout= 1.0) {
      if (FALSE === ($msg= $this->_cmd(NULL, $timeout))) {
        return FALSE;
      }
      
      if (!empty($msg)) printf("recvmsg(%0.2f):= %s\n", $timeout, var_export($msg, 1));
      return $msg;
    }
    
    /**
     * Close connection
     *
     * @access  public
     */
    function close() {
      if ($this->_sock->isConnected()) {
        $this->_cmd('QUIT');
        $this->_sock->close();
      }
    }
    
    function _quote($str) {
      $ret= '';
      for ($i= 0, $s= strlen($str); $i < $s; $i++) {
        if (ord($str{$i}) > 31) {
          $ret.= $str{$i};
        } elseif ("\n" == $str{$i}) {
          $ret.= "\n";
        } else {
          $ret.= sprintf('\x%02X', ord($str{$i}));
        }
      }
      return $ret;
    }
    
    /**
     * This is the core function for read/write IO. It also handles the PING :/PONG :
     * mechanism and will punt on ERROR :
     *
     * @access  public
     * @param   string formatstring If NULL, nothing will be sent
     * @param	string* formatargs
     * @return  string data
     * @throws	Exception
     */
    function _cmd() {
      $args= func_get_args();
      if (NULL !== $args[0]) {
        $timeout= 0.3;
        $cmd= vsprintf($args[0]."\n", array_slice($args, 1));

        #IFDEF DEBUG
        #printf(">>> %s", $cmd);
        #ENDIF

        // Write command
        if (FALSE === $this->_sock->write($cmd)) {
          printf("ERROR WRITING\n");
          return FALSE;
        }
      } else {
        $timeout= $args[1];
      }
      
      // Do we have data? Check for 0.3 sec
      $out= '';
      while ($this->_sock->canRead($timeout)) {
      
        // Read 4 kB
        $res= $this->_sock->read(4096);
        
        #IFDEF DEBUG
        printf("<<< %s\n", $this->_quote($res));
        #ENDIF
        
        // On error or zero length read, break out of this loop
        if (FALSE === $res || 0 == strlen($res)) break;
        
        // ERROR :.* means something is probably fucked up
        if ('ERROR :' == substr($res, 0, 7)) {
          return throw(new Exception(substr($res, 7)));
        }
        
        // PING :XXXX is to be answered immediately. 
        // Do not include PING: message in result
        if ('PING :' == substr($res, 0, 6)) {
          $this->_sock->write('PONG :'.substr($res, 6)."\n");
          continue;
        }
        
        $out.= $res;
      }
      
      // Check for empty return
      $this->_message= array();
      if ('' == chop($out)) return $this->_message;
      
      // Parse response into message struct
      $id= $this->nick.'!'.$this->username;
      foreach (preg_split('/[\r\n]+/', chop($out)) as $line) {
        if (':' != $line{0}) {
          printf("??????????? '%s' ???????????\n", $line);
          continue;
        }
        
        $data= explode(' ', substr($line, 1), 4);
        
        #IFDEF DEBUG
        #echo "---> DEBUG IN PARSER LOOP::"; var_export($data);
        #ENDIF
        
        // Server message
        // :irc.oneandone.co.uk 002 thekid :Your host is irc.oneandone.co.uk, running version u2.10.07.0
        if ($data[0] == $this->server) {
          $this->_message[]= array(
            'type'	 => IRC_MSGT_SERVER,
            'server' => $data[0],
            'code'   => $data[1],
            'nick'   => $data[2],
            'data'   => $data[3]
          );
          continue;
        }
        
        // Client message
        // :thekid!timm@pD950B364.dip0.t-ipconnect.de JOIN :#support
        if ($id == substr($data[0], 0, strlen($id))) {
          $this->_message[]= array(
            'type'   => IRC_MSGT_CLIENT,
            'client' => $data[0],
            'code'   => $data[1],
            'nick'   => $this->nick,
            'data'   => $data[2]
          );
          continue;
        }
        
        // Message from other clients
        // ':dpunkt!dragicevic@billard.schlund.de PRIVMSG #support :hi
        list($nick, $user)= explode('!', $data[0]);
        $this->_message[]= array(
          'type'	=> IRC_MSGT_USER,
          'user'    => $user,
          'code'    => $data[1],
          'nick'    => $nick,
          'data'	=> array($data[2], $data[3])
        );
      }
      
      #IFDEF DEBUG
      #var_export($this->_message);
      #ENDIF
      
      return $this->_message;
    }
  }
?>
    
