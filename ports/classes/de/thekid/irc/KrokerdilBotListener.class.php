<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'peer.irc.IRCConnectionListener', 
    'peer.irc.IRCColor',
    'org.dict.DictClient',
    'text.translator.Swabian',
    'peer.Socket',
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
      $tstart    = 0,
      $config    = NULL,
      $lists     = array(),
      $dictc     = NULL,
      $quote     = NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   &util.Properties config
     */
    function __construct(&$config) {
      $this->config= &$config;
      $this->reloadConfiguration();
      $this->tstart= time();

      // Set up DictClient
      $this->dictc= &new DictClient();
      $this->dictc->connect('dict.org', 2628);
      
      // Set up quote client
      $this->quote= &new Socket('ausredenkalender.informatik.uni-bremen.de', 17);
      
      $l= &Logger::getInstance();
      $this->dictc->setTrace($l->getCategory());
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
            $delta= time() - $this->tstart;
            
            // Break it up into days, hours and minutes
            $days= $delta / 86400;
            $delta-= (int)$days * 86400;
            $hours= $delta / 3600;
            $delta-= (int)$hours * 3600;
            $minutes= $delta / 60;
            
            $connection->writeln(
              'NOTICE %s :Uptime: %d Tag(e), %d Stunde(n) und %d Minute(n)',
              $target, $days, $hours, $minutes
            );
            break;

          case 'quote':
            try(); {
              $this->quote->connect();
              do {
                if (!($buf= $this->quote->readLine())) continue;
                $connection->sendMessage(
                  $target, 
                  '%s%s', 
                  IRCColor::forCode(IRC_COLOR_YELLOW), 
                  $buf
                );
              } while (!$this->quote->eof());
              $this->quote->close();
            } if (catch('IOException', $e)) {
              $e->printStackTrace();
              $connection->sendMessage($target, '!%s', $e->getMessage());
              break;
            }
            break;              
            
          case 'whatis':
            try(); {
              $status= $this->dictc->getStatus();
              $connection->sendMessage($target, '-%s', var_export($status, 1));
            } if (catch('IOException', $e)) {
              $e->printStackTrace();

              // We were probably disconnected, so close connection forcibly
              // (just to be sure) and reconnect
              try(); {
                $this->dictc->close();
                $this->dictc->connect('dict.org', 2628);
              } if (catch('IOException', $e)) {
                $e->printStackTrace();

                // Ignore
              }
            }
            
            try(); {
              $definitions= $this->dictc->getDefinition($params, '*');
            } if (catch('Exception', $e)) {
              $e->printStackTrace();
              $connection->sendMessage($target, '!%s', $e->getMessage());
              break;
            }
            
            // Check if we found something
            if (empty($definitions)) {
              $connection->sendMessage(
                $target, 
                '"%s": No match found', 
                $params
              );
              break;
            }
            
            // Make definitions available via www
            $file= &new File(sprintf(
              '%s%swhatis_%s.html',
              rtrim($this->config->readString('whatis_www', 'document_root'), DIRECTORY_SEPARATOR),
              DIRECTORY_SEPARATOR,
              strtolower(preg_replace('/[^a-z0-9_]/i', '_', $params))
            ));
            try(); {
              $file->open(FILE_MODE_WRITE);
              $file->write('<h1>What is "'.$params.'"?</h1>');
              $file->write('<ol>');
              for ($i= 0, $s= sizeof($definitions); $i < $s; $i++) {
                $file->write('<li>Definition: '.$definitions[$i]->getDatabase().')<br/>');
                $file->write('<pre>'.$definitions[$i]->getDefinition().'</pre>');
                $file->write('</li>');
              }
              $file->write('</ol>');
              $file->write('<hr/><small>Generated by '.$connection->user->getNick().' at '.date('r').'</small>');
              $file->close();
            } if (catch('IOException', $e)) {
              $e->printStackTrace();
              $connection->sendMessage(
                $target, 
                '- %s', 
                $e->getMessage()
              );
              break;
            }
            $connection->sendMessage(
              $target, 
              '"%s": Definitions @ %s%s', 
              $params,
              $this->config->readString('whatis_www', 'base_href'),
              urlencode($file->getFileName())
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
        
          case 'maul':
            $this->sendRandomMessage($connection, $target, 'shutup', $params, NULL);
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
        $connection->writeln('NOTICE %s :%s is back!', $channel, $nick);
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
