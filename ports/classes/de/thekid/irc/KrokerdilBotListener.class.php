<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'peer.irc.IRCConnectionListener', 
    'peer.irc.IRCColor',
    'text.translator.Swabian',
    'io.File'
  );

  /**
   * Krokerdil Bot
   *
   * @see      xp://peer.irc.IRCConnectionListener
   * @purpose  IRC Bot
   */
  class KrokerdilBotListener extends IRCConnectionListener {
    var
      $config    = NULL,
      $lists     = array();
      
    /**
     * Constructor
     *
     * @access  public
     * @param   &util.Properties config
     */
    function __construct(&$config) {
      parent::__construct();
      $this->config= &$config;
      $this->reloadConfiguration();
    }
    
    /**
     * Reload Bot configuration
     *
     * @access  protected
     */
    function reloadConfiguration() {
      $this->config->reset();
      $this->lists= array();
      
      // Set base directory for lists relative to that of the config file's
      $base= dirname($this->config->getFilename()).DIRECTORY_SEPARATOR;
      
      // Read word/message lists
      foreach ($this->config->readSection('lists') as $identifier => $file) {
        $this->lists[$identifier]= array();
        $f= &new File($base.$file);
        try(); {
          if ($f->open(FILE_MODE_READ)) while (($line= $f->readLine()) && !$f->eof()) {
            $this->lists[$identifier][]= $line;
          }
          $f->close();
        } if (catch('IOException', $e)) {
          $e->printStackTrace();
          return FALSE;
        }
      }
    }
    
    /**
     * Sends to a target, constructing it from a random element within a specified
     * list.
     *
     * @access  private
     * @param   &peer.irc.IRCConnection connection
     * @param   string target
     * @param   string list list identifier
     * @param   string nick
     * @param   string message
     * @return  bool success
     */
    function sendRandomMessage(&$connection, $target, $list, $nick, $message) {
      $format= $this->lists[$list][rand(0, sizeof($this->lists[$list])- 1)];
      if (empty($format)) return;
      
      if ('/me' == substr($format, 0, 3)) {
        $r= $connection->sendAction(
          $target, 
          substr($format, 4),
          $nick,
          $channel,
          $message
        );
      } else {
        $r= $connection->sendMessage(
          $target, 
          $format,
          $nick,
          $channel,
          $message
        );
      }
      return $r;
    }
    
    /**
     * Callback for private messages
     *
     * @access  public
     * @param   &peer.irc.IRCConnection connection
     * @param   string nick
     * @param   string target
     * @param   string message
     */
    function onPrivateMessage(&$connection, $nick, $target, $message) {
    
      // Commands
      if (sscanf($message, "!%s %[^\r]", $command, $params)) {
        switch (strtolower($command)) {
          case 'reload':
            if ($this->config->readString('control', 'password') == $params) {
              $this->reloadConfiguration();
              $connection->sendAction($nick, 'received SIGHUP and reloads his configuration');
            } else {
              $connection->sendMessage($nick, 'Nice try, but >%s< is incorrect', $params);
            }
            break;
          
          case 'uptime':
            $r= getrusage();
            list($days, $hours, $minutes)= explode('-', strftime('%d-%H-%M', $r['ru_utime.tv_sec']));
            $connection->sendAction(
              $target, 
              '\'s uptime ist %d Tag(e), %d Stunde(n) und %d Minute(n)',
             $days- 1, $hours- 1, $minutes
            );
            break;
          
          case 'say':
            list($dest, $message)= explode(' ', $params, 2);
            $connection->sendMessage($dest, $message);
            break;

          case 'do':
            list($dest, $action)= explode(' ', $params, 2);
            $connection->sendAction($dest, $action);
            break;
          
          case 'schwob':
            $connection->sendMessage($target, Swabian::translate($params));
            break;

          case 'bite':
            $connection->sendAction($target, 'beißt %s', $params);
            break;
            
          case 'beep':
          case 'hup':
            $connection->sendAction($target, 'hupt (%s)'."\7", $params);
            break;
          
          case 'falsch':
            $connection->sendMessage(
              $target, 
              '%s ist zwar süß, ABER %sFALSCH!', 
              $params, 
              IRCColor::forCode(IRC_COLOR_RED)
            );
            break;
          
          case 'i':
          case 'idiot':
            if ('#' == $params{0}) {    // Allow #<channel>/<nick> so private messages work
              list($target, $params)= explode('/', $params);
            }
            $connection->sendMessage(
              $target, 
              '%s ist ein %s', 
              $params, 
              $this->lists['swears'][rand(0, sizeof($this->lists['swears'])- 1)]
            );
            break;

          case 'i+':
          case 'idiot+':
            if (in_array($params, $this->lists['swears'])) {
              $connection->sendAction(
                $target, 
                'kannte das Schimpfwort >%s%s%s< schon', 
                IRCColor::forCode(IRC_COLOR_ORANGE),
                $params, 
                IRCColor::forCode(IRC_COLOR_DEFAULT)
              );
              break;                          
            }
            
            // Update swears array
            $this->lists['swears'][]= $params;
            
            // Also update the swears file
            $f= &new File(sprintf(
              '%s%s%s',
              dirname($this->config->getFilename()),
              DIRECTORY_SEPARATOR,
              $this->config->readString('lists', 'swears')
            ));
            try(); {
              $f->open(FILE_MODE_APPEND);
              $f->write($params."\n");
              $f->close();
            } if (catch('IOException', $e)) {
              $connection->sendMessage($target, '! '.$e->getMessage());
              break;
            }
            $connection->sendAction($target, 'hat jetzt %d Schimpfwörter', sizeof($this->lists['swears']));
            break;              

          case 'ascii':
            $connection->sendMessage($target, 'ASCII #%d = %s', $params, chr($params));
            break;
        }
        return;
      }
      
      // Any other phrase containing my name
      if (stristr($message, $connection->user->getNick())) {
        $this->sendRandomMessage($connection, $target, 'talkback', $nick, $message);
        return;
      }
      
      // Produce random noise
      if (15 == rand(0, 30)) {
        $this->sendRandomMessage($connection, $target, 'noise', $nick, $message);
      }
    }

    /**
     * Callback for server message REPLY_ENDOFMOTD (376)
     *
     * @access  public
     * @param   &peer.irc.IRCConnection connection
     * @param   string server
     * @param   string target whom the message is for
     * @param   string data
     */
    function onEndOfMOTD(&$connection, $server, $target, $data) {
      if ($this->config->hasSection('autojoin')) {
        $connection->join(
          $this->config->readString('autojoin', 'channel'),
          $this->config->readString('autojoin', 'password', NULL)
        );
      }
    }    

    /**
     * Callback for invitations
     *
     * @access  public
     * @param   &peer.irc.IRCConnection connection
     * @param   string nick sending the invitation
     * @param   string who who is invited
     * @param   string channel invitation is for
     */
    function onInvite(&$connection, $nick, $who, $channel) {
      if ($this->config->readBool('invitations', 'follow', FALSE)) {
        $connection->join($channel);
      }
    }
  
    /**
     * Callback for kicks
     *
     * @access  public
     * @param   &peer.irc.IRCConnection connection
     * @param   string channel the channel the user was kicked from
     * @param   string nick that initiated the kick
     * @param   string who who was kicked
     * @param   string reason what reason the user was kicked for
     */
    function onKicks(&$connection, $channel, $nick, $who, $reason) {
      if (strcasecmp($who, $connection->user->getNick()) == 0) {
        $connection->join($channel);
        $connection->sendMessage($nick, 'He! "%s" ist KEIN Grund', $reason);
        $connection->sendAction($channel, '%s kickt arme unschuldige Bots, und das wegen so etwas lumpigem wie %s', $nick, $reason);
      }
    }
  
    /**
     * Callback for joins
     *
     * @access  public
     * @param   &peer.irc.IRCConnection connection
     * @param   string channel which channel was joined
     * @param   string nick who joined
     */
    function onJoins(&$connection, $channel, $nick) {
      if (strcasecmp($nick, $connection->user->getNick()) == 0) {
        $connection->writeln("NOTICE %s :%s is back!", $channel, $nick);
      } else {
        $this->sendRandomMessage($connection, $channel, 'join', $nick, NULL);
      }
    }

    /**
     * Callback for parts
     *
     * @access  public
     * @param   &peer.irc.IRCConnection connection
     * @param   string channel which channel was part
     * @param   string nick who part
     * @param   string message the part message, if any
     */
    function onParts(&$connection, $channel, $nick, $message) {
      $this->sendRandomMessage($connection, $channel, 'leave', $nick, $message);
    }

    /**
     * Callback for actions. Actions are when somebody writes /me ...
     * in their IRC window.
     *
     * @access  public
     * @param   &peer.irc.IRCConnection connection
     * @param   string nick who initiated the action
     * @param   string target where action was initiated
     * @param   string action what actually happened (e.g. "looks around")
     */
    function onAction(&$connection, $nick, $target, $params) {
      if (10 == rand(0, 20)) {
        $connection->sendAction($target, 'macht %s nach und %s auch', $nick, $params);
      }
    }
    
    /**
     * Callback for CTCP VERSION
     *
     * @access  public
     * @param   &peer.irc.IRCConnection connection
     * @param   string nick nick requesting version
     * @param   string target where version was requested
     * @param   string params additional parameters
     */
    function onVersion(&$connection, $nick, $target, $params) {
      $connection->writeln('NOTICE %s :%sVERSION Krokerdil $Revision$%s', $nick, "\1", "\1");
    }
  }
?>
