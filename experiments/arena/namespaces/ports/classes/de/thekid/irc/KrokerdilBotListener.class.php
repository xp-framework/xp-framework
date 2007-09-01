<?php
/* This class is part of the XP framework
 *
 * $Id: KrokerdilBotListener.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace de::thekid::irc;

  ::uses(
    'peer.irc.IRCConnectionListener',
    'peer.irc.IRCColor',
    'org.dict.DictClient',
    'net.schweikhardt.Swabian',
    'peer.Socket',
    'io.File',
    'io.FileUtil',
    'util.Date'
  );

  define('MODE_AWAKE',      1);
  define('MODE_SLEEP',      2);

  /**
   * Krokerdil Bot
   *
   * @see      xp://peer.irc.IRCConnectionListener
   * @purpose  IRC Bot
   */
  class KrokerdilBotListener extends peer::irc::IRCConnectionListener {
    public
      $tstart       = 0,
      $registry     = array(),
      $config       = NULL,
      $lists        = array(),
      $karma        = array(),
      $recognition  = array(),
      $dictc        = NULL,
      $quote        = NULL,
      $operator     = array(),
      $channels     = array(),
      $mode         = 0;
      
    /**
     * Constructor
     *
     * @param   &util.Properties config
     */
    public function __construct($config) {
      $this->config= $config;
      $this->reloadConfiguration();
      $this->tstart= time();
      $this->mode= MOOD_AWAKE;
      
      // Initially schedule sleeping
      $ts= time() + 86400;
      $this->registry['scheduled-sleep']= ($ts - ($ts % 86400));
      $this->registry['laststore']= time();

      // Set up DictClient
      $this->dictc= new org::dict::DictClient();
      
      // Set up quote client
      $this->quote= new peer::Socket('ausredenkalender.informatik.uni-bremen.de', 17);
      
      $l= util::log::Logger::getInstance();
      $this->cat= $l->getCategory();
      $this->dictc->setTrace($this->cat);
    }
    
    /**
     * Reload Bot configuration
     *
     */
    public function reloadConfiguration() {
      $this->config->reset();
      $this->lists= array();
      
      // Set base directory for lists relative to that of the config file's
      $base= dirname($this->config->getFilename()).DIRECTORY_SEPARATOR;
      
      // Read word/message lists
      foreach ($this->config->readSection('lists') as $identifier => $file) {
        $this->lists[$identifier]= array();
        $f= new io::File($base.$file);
        try {
          if ($f->open(FILE_MODE_READ)) while (($line= $f->readLine()) && !$f->eof()) {
            $this->lists[$identifier][]= $line;
          }
          $f->close();
        } catch (io::IOException $e) {
          $e->printStackTrace();
          return FALSE;
        }
      }
      
      // Read karma recognition phrases
      $f= new io::File($base.$this->config->readString('karma', 'recognition'));
      try {
        if ($f->open(FILE_MODE_READ)) while (!$f->eof()) {
          $line= $f->readLine();
          if (empty($line) || strspn($line, ';#')) continue;
          
          list($pattern, $channel, $direct)= explode(':', $line);
          $this->recognition[$pattern]= array((int)$channel, (int)$direct);
        }
        $f->close();
      } catch (io::IOException $e) {
        $e->printStackTrace();
        return FALSE;
      }
      
      // If no karma is set and the karma storage exists, load it
      if (0 == sizeof($this->karma)) {
        try {
          $f= new io::File($base.'karma.list');
          if ($f->exists()) {
            $karma= unserialize(io::FileUtil::getContents($f));
            if ($karma) $this->karma= $karma;
          }
        } catch (io::IOException $e) {
        
          // Karma loading failed - log, but ignore...
          $this->cat && $this->cat->error($e);
        }
      }
    }
    
    /**
     * Save current karma configuration to disk.
     *
     */
    public function storeConfiguration() {
    
      // Set base directory for lists relative to that of the config file's
      $base= dirname($this->config->getFilename()).DIRECTORY_SEPARATOR;

      try {
        $f= new io::File($base.'karma.list');
        $f->open(FILE_MODE_WRITE);
        
        $f->write(serialize($this->karma));
        $f->close();
      } catch (io::IOException $e) {
        $this->cat && $this->cat->error($e);
      }
      
      $this->registry['laststore']= time();
    }
    
    /**
     * Sends to a target, constructing it from a random element within a specified
     * list.
     *
     * @param   &peer.irc.IRCConnection connection
     * @param   string target
     * @param   string list list identifier
     * @param   string nick
     * @param   string message
     * @return  bool success
     */
    public function sendRandomMessage($connection, $target, $list, $nick, $message) {
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
     * Helper method for privileged actions.
     *
     * @param   &peer.irc.IRCConnection connection
     * @param   string nick
     * @param   string password
     * @return  bool
     */
    public function doPrivileged($connection, $nick, $password) {
      if ($this->config->readString('control', 'password') == $password) return TRUE;
      
      $connection->sendMessage($nick, 'Nice try, but >%s< is incorrect', $password);
      return FALSE;
    }
    
    /**
     * Helper method to set karma for a nick. Also handles karma floods
     *
     * @param   string nick
     * @param   int delta
     * @param   string reason default NULL
     */
    public function setKarma($nick, $delta, $reason= NULL) {
      static $last= array();
      
      if (!isset($this->karma[$nick])) {
        $this->karma[$nick]= 0; // Neutral
      }
      
      if (0 == $delta) return;  // Short-cuircit this
      
      if ($reason && isset($last[$nick][$reason]) && (time() - $last[$nick][$reason] <= 2)) {
        $this->cat && $this->cat->warnf(
          'Karma flood from %s (last karma for %s set at %s)', 
          $nick,
          $reason,
          date('r', $last[$nick][$reason])
        );
        $this->karma[$nick]-= 10;
      } else {
        $this->karma[$nick]+= $delta;
        $last[$nick]= array();
      }

      $this->cat && $this->cat->debugf(
        'Changing karma for %s by %d because of %s (total: %d)', 
        $nick,
        $delta,
        $reason ? $reason : '(null)',
        $this->karma[$nick]
      );
      $last[$nick][$reason]= time();
    }
    
    /**
     * Callback for mode changes
     *
     * @param   &peer.irc.IRCConnection connection
     * @param   string nick who initiated the mode change
     * @param   string target what the mode setting is for (e.g. +k #channel, +i user)
     * @param   string mode the mode including a + or - as its first letter
     * @param   string params additional parameters
     */
    public function onModeChanges($connection, $nick, $target, $mode, $params) { 
      if (strcasecmp($params, $connection->user->getNick()) != 0) return;

      $delta= ('-' == $mode[0] ? -10 : 10);
      $this->setKarma($nick, $delta, '@@mode.'.$mode);
      if (MODE_AWAKE == $this->mode) $this->sendRandomMessage(
        $connection, 
        $target, 
        $delta < 0 ? 'karma.dislike' : 'karma.like', 
        $nick,
        $params
      );
      
      // Check to see if we are channel op
      if (strstr($mode, 'o')) {
        if ('+' == $mode{0}) {
          $this->operator[$target]= TRUE;
        } else {
          $this->operator[$target]= FALSE;
        }
        
        $this->cat && $this->cat->debug('OP', $this->operator);
      }
    }

    /**
     * Callback for nick changes
     *
     * @param   &peer.irc.IRCConnection connection
     * @param   string nick the old nick
     * @param   string new the new nick
     */
    public function onNickChanges($connection, $nick, $new) {
      $this->cat && $this->cat->debug('Moving karma from', $nick, 'to', $new);
      $this->setKarma($new, $this->karma[$nick]);
      unset($this->karma[$nick]);
    }
    
    /**
     * Checks whether a given message contains a swear and returns it if 
     * found, NULL otherwise.
     *
     * @param   string message
     * @return  string swear
     */
    public function containsSwear($message) {
      for ($i= 0, $s= sizeof($this->lists['swears']); $i < $s; $i++) {
        $pattern= '/('.str_replace(' ', ')? (', preg_quote($this->lists['swears'][$i])).')/i';
        if (preg_match($pattern, $message)) {
          $this->cat && $this->cat->debug('"'.$message.'" matched pattern', $pattern);
          return $this->lists['swears'][$i];
        }
      }
      $this->cat && $this->cat->debug('"'.$message.'": Contains no swear');
      return NULL;
    }

    /**
     * Callback for private messages (for default mood).
     *
     * @param   &peer.irc.IRCConnection connection
     * @param   string nick
     * @param   string target
     * @param   string message
     */
    public function handlePrivateMessageDefault($connection, $nick, $target, $message) {

      // Commands
      if (sscanf($message, "!%s %[^\r]", $command, $params)) {
        switch (strtolower($command)) {
          case '@reload':
            if ($this->doPrivileged($connection, $nick, $params)) {
              $this->reloadConfiguration();
              $connection->sendAction($nick, 'received SIGHUP and reloads his configuration');
            }
            break;
          
          case '@shutdown':
            if ($this->doPrivileged($connection, $nick, $params)) {
              $this->storeConfiguration();
              $connection->writeln(
                'QUIT %s scheisst auf euch',
                $connection->user->getNick()
              );
              $connection->close();
              
              // Needed, because the IRCConnection is used in while (1) ...
              exit;
            }
            break;
          
          case '@sleep':
            list($duration, $password)= explode(' ', $params);
            if ($this->doPrivileged($connection, $nick, $password)) {
              $this->doSleep($connection, $duration);
            }
            break;
          
          case '@changenick':
            list($new_nick, $password)= explode(' ', $params);
            if ($this->doPrivileged($connection, $nick, $password)) {
              $connection->setNick($new_nick);
            }
            break;
          
          case '@karma':
            if ($this->doPrivileged($connection, $nick, $params)) {
              foreach ($this->karma as $name => $value) {
                $connection->sendMessage(
                  $nick,
                  '%s: %d', 
                  $name,
                  $value
                );
              }
            }
            break;
          
          case '@setkarma':
            list($who, $value, $password)= explode(' ', $params);
            if ($this->doPrivileged($connection, $nick, $password)) {
              $this->setKarma($who, (int)$value);
            }
            break;
                     
          case '@kick':
            list($channel, $victim, $password)= explode(' ', $params);
            if ($this->doPrivileged($connection, $nick, $password)) {
              $connection->writeln(
                'KICK %s %s :%s', 
                $channel, 
                $victim, 
                sprintf($this->lists['karma.dislike'][rand(0, sizeof($this->lists['karma.dislike'])- 1)], $victim)
              );
            }
            break;
          
          case '@join':
            list($channel, $channelpass, $password)= explode(' ', $params);
            if (empty($password)) { $password= $channelpass; $channelpass= NULL; }
            if ($this->doPrivileged($connection, $nick, $password)) {
              $connection->join($channel, $channelpass);
            }
            break;
          
          case '@part':
            list($channel, $password)= explode(' ', $params);
            if ($this->doPrivileged($connection, $nick, $password)) {
              $connection->part($channel);
              unset($this->operator[$channel]);
              unset($this->channels[$channel]);
            }
            break;
          
          case '@channels':
            if ($this->doPrivileged($connection, $nick, $params)) {
              $chans= '';
              foreach (array_keys($this->channels) as $c) {
                $chans.= sprintf('%s%s ', ($this->operator[$c] ? '@' : ''), $c);
              }
              
              $connection->sendMessage($nick, $chans);
            }
            break;
          
          case '@op':
          case '@deop':
            list($channel, $nickname, $password)= explode(' ', $params);
            if ($this->doPrivileged($connection, $nick, $password)) {
              if (isset($this->operator[$channel]) && TRUE === $this->operator[$channel]) {
                $connection->writeln(
                  'MODE %s %s %s',
                  $channel,
                  (strtolower($command) == '@op' ? '+o' : '-o'),
                  $nickname
                );
              } else {
                $connection->sendMessage($nick, 'Ich bin kein Operator in %s', $channel);
              }
            }
            break;
          
          case 'karma':
            $this->setKarma($nick, 0);  // Make sure array is initialized
            $connection->sendMessage($target, 'Karma für %s: %d', $nick, $this->karma[$nick]);
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

            $this->setKarma($nick, 1, '@@uptime');
            break;

          case 'quote':
            try {
              if ($this->quote->connect()) do {
                if (!($buf= $this->quote->readLine())) continue;
                $connection->sendMessage(
                  $target, 
                  '%s%s', 
                  peer::irc::IRCColor::forCode(IRC_COLOR_YELLOW), 
                  $buf
                );
              } while (!$this->quote->eof());
              $this->quote->close();
            } catch (io::IOException $e) {
              $e->printStackTrace();
              $connection->sendMessage($target, '!%s', $e->getMessage());
              break;
            }
            break;              
            
          case 'whatis':
            try {
              if ($this->dictc->connect('dict.org', 2628)) {
                $definitions= $this->dictc->getDefinition($params, '*');
              }
              $this->dictc->close();
            } catch (::Exception $e) {
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
            $file= new io::File(sprintf(
              '%s%swhatis_%s.html',
              rtrim($this->config->readString('whatis_www', 'document_root'), DIRECTORY_SEPARATOR),
              DIRECTORY_SEPARATOR,
              strtolower(preg_replace('/[^a-z0-9_]/i', '_', $params))
            ));
            try {
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
            } catch (io::IOException $e) {
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
            $connection->sendMessage($target, net::schweikhardt::Swabian::translate($params));
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
              peer::irc::IRCColor::forCode(IRC_COLOR_RED)
            );
            break;
        
          case 'kap0tt':
          case 'kaputt':
            $connection->sendMessage(
              $target, 
              '%s ist zwar toll, ABER %sKAP0TT!', 
              $params, 
              peer::irc::IRCColor::forCode(IRC_COLOR_YELLOW)
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
            
            // Don't insult yourself - instead, insult the user:) Check on similar text
            // so people can't get away with misspelling the name. We might accidentally
            // also insult users with similar names than ours, but, hey, their fault.
            similar_text(strtolower($params), strtolower($connection->user->getNick()), $percent);
            if ($percent >= 75) {
              $params= $nick;
              $format= '%s, du bist %s';
              $this->setKarma($nick, -5, '@@idiot');
              $connection->sendAction($target, 'beleidigt sich nicht selbst');
            } else {
              $format= '%s ist %s';
            }
            
            $connection->sendMessage(
              $target, 
              $format, 
              $params, 
              $this->lists['swears'][rand(0, sizeof($this->lists['swears'])- 1)]
            );
            break;

          case 'i+':
          case 'idiot+':
            if (stristr($params, $connection->user->getNick())) {
              $connection->sendAction($target, 'wird seinen eigenen Namen nicht als Schimpfwort akzeptieren');
              $this->setKarma($nick, -2, '@@lamer');
              return;
            }
            
            // Don't accept new swears from disliked nicks.
            if ($this->karma[$nick] < 0) {
              $connection->sendAction($target, 'hörte weg, als %s das Schimpfwort in den Raum wurf.', $nick);
              $this->setKarma($nick, -1, '@@lamer');
              return;
            }

            if (in_array($params, $this->lists['swears'])) {
              $connection->sendAction(
                $target, 
                'kannte das Schimpfwort >%s%s%s< schon', 
                peer::irc::IRCColor::forCode(IRC_COLOR_ORANGE),
                $params, 
                peer::irc::IRCColor::forCode(IRC_COLOR_DEFAULT)
              );
              break;                          
            }
            
            // Update swears array
            $this->lists['swears'][]= $params;
            
            // Also update the swears file
            $f= new io::File(sprintf(
              '%s%s%s',
              dirname($this->config->getFilename()),
              DIRECTORY_SEPARATOR,
              $this->config->readString('lists', 'swears')
            ));
            try {
              $f->open(FILE_MODE_APPEND);
              $f->write($params."\n");
              $f->close();
            } catch (io::IOException $e) {
              $connection->sendMessage($target, '! '.$e->getMessage());
              break;
            }
            $connection->sendAction($target, 'hat jetzt %d Schimpfwörter', sizeof($this->lists['swears']));
            break;              
          
          case 'i-':
          case 'idiot-':
          
            // Don't accept swear removals from disliked nicks.
            if ($this->karma[$nick] < 0) {
              $connection->sendAction($target, 'hörte weg, als %s das Schimpfwort in den Raum wurf.', $nick);
              $this->setKarma($nick, -1, '@@lamer');
              return;
            }
            
            if (FALSE === ($index= array_search($params, $this->lists['swears']))) {
              $connection->sendAction($target, 'kennt kein solches Wort.');
              break;
            }
            
            // Remove from memory list.
            unset($this->lists['swears'][$index]);
            
            // Store the removed word
            $f= new io::File(sprintf(
              '%s%s%s.deleted',
              dirname($this->config->getFilename()),
              DIRECTORY_SEPARATOR,
              $this->config->readString('lists', 'swears')
            ));
            try {
              $f->open(FILE_MODE_APPEND);
              $f->write($params."\n");
              $f->close();
            } catch (io::IOException $e) {
              $connection->sendMessage($target, '! '.$e->getMessage());
              break;
            }
            
            // Also update the swears file
            $f= new io::File(sprintf(
              '%s%s%s',
              dirname($this->config->getFilename()),
              DIRECTORY_SEPARATOR,
              $this->config->readString('lists', 'swears')
            ));
            try {
              $f->open(FILE_MODE_WRITE);
              $f->write(implode("\n", $this->lists['swears']));
              $f->close();
            } catch (io::IOException $e) {
              $connection->sendMessage($target, '! '.$e->getMessage());
              break;
            }
            
            $connection->sendMessage($target, 'Und wieder bin ich ein Stückchen anständiger geworden.');
            break;

          case 'ascii':
            $connection->sendMessage($target, 'ASCII #%d = %s', $params, chr($params));
            break;
        }
        return;
      }
      
      // Any other phrase containing my name
      if (stristr($message, $connection->user->getNick())) {
        $karma= 0;
        $recognized= FALSE;
        
        // See if we can recognize something here and calculate karma - multiplied
        // by a random value because this message is directed at me.
        foreach ($this->recognition as $pattern => $delta) {
          if (!preg_match($pattern, $message)) continue;
          $karma= rand(1, 5) * $delta[1];
          $recognized= TRUE;
          $this->setKarma($nick, $karma, $pattern);
        }

        // Check our bad words list
        if (!$recognized && ($swear= $this->containsSwear($message))) {
          $this->cat && $this->cat->debug($nick, 'said', $swear, 'to me, no good');
          $karma= rand(-5, -1);
          $recognized= TRUE;
          $this->setKarma($nick, $karma, '@@swear');
        }
        
        // Don't know what to do with this, say something random
        if (!$recognized) {
          $this->sendRandomMessage($connection, $target, 'talkback', $nick, $message);
          return;      
        }

        // Send a karma-based message
        $this->sendRandomMessage(
          $connection, 
          $target, 
          $karma < 0 ? 'karma.dislike' : 'karma.like', 
          $nick, 
          $message
        );
        return;
      }
      
      // Random actions
      switch (rand(0, 30)) {
        case 2:
          if (!$this->operator[$target]) break;

          // Kick a random person with a very bad karma
          $victim= array_rand($this->karma);
          if ($this->karma[$victim] < -50) {
            $connection->writeln(
              'KICK %s %s :%s', 
              $target, 
              $victim, 
              sprintf($this->lists['karma.dislike'][rand(0, sizeof($this->lists['karma.dislike'])- 1)], $victim)
            );
          }
          break;

        case 10:
          if ($swear= $this->containsSwear($message)) {
            $this->setKarma($nick, -1, $pattern, $swear);
          }
          break;
                
        case 15:
          $this->sendRandomMessage($connection, $target, 'noise', $nick, $message);
          break;

        case 16:
          $this->sendRandomMessage(
            $connection, 
            $target, 
            $this->karma[array_rand($this->karma)] < 0 ? 'karma.dislike' : 'karma.like', 
            $nick, 
            $message
          );
          break;
      }

      // Karma recognition
      foreach ($this->recognition as $pattern => $delta) {
        if (!preg_match($pattern, $message)) continue;
        $this->setKarma($nick, $delta[0], $pattern);
      }
    }
    
    /**
     * Callback for private messages in Sleeping mood
     *
     * @param   &peer.irc.IRCConnection connection
     * @param   string nick
     * @param   string target
     * @param   string message
     */
    public function handlePrivateMessageSleeping($connection, $nick, $target, $message) {
    
      // Auto-Wakeup after the specified period of time
      if (time() > $this->registry['wakeup']) {
        $this->doWakeup($connection);
        return;
      }

      // Commands
      if (sscanf($message, "!%s %[^\r]", $command, $params)) {
        switch (strtolower($command)) {
          case '@wakeup':
            if ($this->doPrivileged($connection, $nick, $params)) {
              $this->doWakeup($connection);
            }
            break;
        }
      }

      // Any other phrase containing my name
      if (stristr($message, $connection->user->getNick())) {
      
        // Talking with me while sleeping is regarded as disturbing, so 
        // add negative karma for that person.
        $this->setKarma($nick, rand(-5, 0), '@@sleeping');
        
        if (rand(0, 100) > 90) {
          if (rand(0, 100) % 2) {
            $connection->sendMessage(
              $target, 
              '%s ist %s', 
              $nick,
              $this->lists['swears'][rand(0, sizeof($this->lists['swears'])- 1)]
            );
          } else {
            $connection->sendMessage($target, 'Siehst du nicht, dass ich schlafe, %s?!', $nick);
          }
        }
      }
    }
        
    /**
     * Perform falling asleep.
     *
     * @param   &peer.irc.IRCConnection connection
     * @param   int time time to sleep
     */
    public function doSleep($connection, $time) {
      if ($this->mode == MODE_SLEEP) return;
      
      $this->cat && $this->cat->debugf('Going to sleep for %d seconds', $time);
      $this->mode= MODE_SLEEP;
      
      // Schedule wakeup
      $this->registry['wakeup']= time() + $time;
      $this->cat && $this->cat->debug('Scheduled wakeup time is', new util::Date($this->registry['wakeup']));
      
      // Reset sleep
      unset($this->registry['scheduled-sleep']);
      
      // Announce change to sleep mode, then change nick
      foreach (array_keys($this->channels) as $channel) {
        $this->sendRandomMessage($connection, $channel, 'sleep', NULL, NULL);
      }
      
      $this->registry['nick-awake']= $connection->user->getNick();
      $connection->setNick($connection->user->getNick().'|zZz');
    }

    /**
     * Perform wakeup.
     *
     * @param   &peer.irc.IRCConnection connection
     */
    public function doWakeup($connection) {
      if ($this->mode == MODE_AWAKE) return;
      
      $this->cat && $this->cat->debug('The bot is waking up again. Scheduled wake time was',
        new util::Date($this->registry['wakeup'])
      );
      $this->mode= MODE_AWAKE;
      
      // Reset wakup, schedule next sleep
      unset($this->registry['wakeup']);
      
      // Schedule next sleeping time
      $ts= time() + 86400;
      $this->registry['scheduled-sleep']= $ts - ($ts % 86400);
      $this->cat && $this->cat->debug('Next scheduled sleep is', new util::Date($this->registry['scheduled-sleep']));
      
      // Restore nick name and announce wakeup in all joined channels
      $connection->setNick($this->registry['nick-awake']);
      foreach (array_keys($this->channels) as $channel) {
        $this->sendRandomMessage($connection, $channel, 'wakeup', NULL, NULL);
      }
    }
    
    /**
     * Callback for private messages
     *
     * @param   &peer.irc.IRCConnection connection
     * @param   string nick
     * @param   string target
     * @param   string message
     */
    public function onPrivateMessage($connection, $nick, $target, $message) {
      switch ($this->mode) {
        case MODE_AWAKE:
        default:
          $this->handlePrivateMessageDefault($connection, $nick, $target, $message);
          break;

        case MODE_SLEEP:
          $this->handlePrivateMessageSleeping($connection, $nick, $target, $message);
          break;
      }
    }

    /**
     * Callback for server message REPLY_ENDOFMOTD (376)
     *
     * @param   &peer.irc.IRCConnection connection
     * @param   string server
     * @param   string target whom the message is for
     * @param   string data
     */
    public function onEndOfMOTD($connection, $server, $target, $data) {
      if ($this->config->hasSection('autojoin')) {
        $hash= $this->config->readHash('autojoin', 'channels');
        foreach ($hash->keys() as $channel) {
          
          if (is_numeric($channel)) {
            $channel= $hash->get($channel);
            $pass= NULL;
          } else {
            $pass= $hash->get($channel);
          }
          $connection->join($channel, $pass);
        }
      }
    }

    /**
     * Callback for invitations
     *
     * @param   &peer.irc.IRCConnection connection
     * @param   string nick sending the invitation
     * @param   string who who is invited
     * @param   string channel invitation is for
     */
    public function onInvite($connection, $nick, $who, $channel) {
      if ($this->config->readBool('invitations', 'follow', FALSE)) {
        $connection->join($channel);
        $this->setKarma($nick, 5, '@@invite');
      }
    }
  
    /**
     * Callback for kicks
     *
     * @param   &peer.irc.IRCConnection connection
     * @param   string channel the channel the user was kicked from
     * @param   string nick that initiated the kick
     * @param   string who who was kicked
     * @param   string reason what reason the user was kicked for
     */
    public function onKicks($connection, $channel, $nick, $who, $reason) {
      if (strcasecmp($who, $connection->user->getNick()) == 0) {
        $connection->join($channel);
        $connection->sendMessage($nick, 'He! "%s" ist KEIN Grund', $reason);
        $connection->sendAction($channel, '%s kickt arme unschuldige Bots, und das wegen so etwas lumpigem wie %s', $nick, $reason);

        $this->setKarma($nick, -10, '@@kick');
      } else {
        $this->setKarma($who, -5, '@@kicked');
      }
    }
  
    /**
     * Callback for joins
     *
     * @param   &peer.irc.IRCConnection connection
     * @param   string channel which channel was joined
     * @param   string nick who joined
     */
    public function onJoins($connection, $channel, $nick) {
      if (strcasecmp($nick, $connection->user->getNick()) == 0) {
        $connection->writeln('NOTICE %s :%s is back!', $channel, $nick);
        $this->channels[$channel]= TRUE;
      } else {
        if ($this->mode == MODE_AWAKE) {
          $this->sendRandomMessage($connection, $channel, 'join', $nick, NULL);
        }
      }
    }

    /**
     * Callback for parts
     *
     * @param   &peer.irc.IRCConnection connection
     * @param   string channel which channel was part
     * @param   string nick who part
     * @param   string message the part message, if any
     */
    public function onParts($connection, $channel, $nick, $message) {
      if (strcasecmp($nick, $connection->user->getNick()) == 0) {
        $this->channels[$channel]= FALSE;
      }
      
      if ($this->mode == MODE_AWAKE)
        $this->sendRandomMessage($connection, $channel, 'leave', $nick, $message);
    }

    /**
     * Callback for actions. Actions are when somebody writes /me ...
     * in their IRC window.
     *
     * @param   &peer.irc.IRCConnection connection
     * @param   string nick who initiated the action
     * @param   string target where action was initiated
     * @param   string action what actually happened (e.g. "looks around")
     */
    public function onAction($connection, $nick, $target, $params) {
      if (MODE_AWAKE != $this->mode) return;
      
      if (10 == rand(0, 20)) {
        $connection->sendAction($target, 'macht %s nach und %s auch', $nick, $params);
        $this->setKarma($nick, 1, '@@imitate');
      }

      // Karma recognition
      foreach ($this->recognition as $pattern => $delta) {
        if (!preg_match($pattern, $message)) continue;
        $this->setKarma($nick, $delta[0], $pattern);
      }
    }
    
    /**
     * Callback for CTCP VERSION
     *
     * @param   &peer.irc.IRCConnection connection
     * @param   string nick nick requesting version
     * @param   string target where version was requested
     * @param   string params additional parameters
     */
    public function onVersion($connection, $nick, $target, $params) {
      $connection->writeln('NOTICE %s :%sVERSION Krokerdil $Revision: 8971 $%s', $nick, "\1", "\1");
    }
    
    /**
     * Callback for PING
     *
     * @param   &peer.irc.IRCConnection connection
     * @param   string data
     */
    public function onPings($connection, $data) {
    
      // Automatically store configuration every hour
      if (time() > $this->registry['laststore'] + 3600) {
        $this->storeConfiguration();
      }
    
      if (
        rand(0, 100) > 95 &&
        isset($this->registry['scheduled-sleep']) &&
        $this->registry['scheduled-sleep'] < time()
      ) {
      
        // Maximally sleep up to from around 2,5 to 8 hours per day
        $this->doSleep($connection, 60 * 5 * rand(30, 100));
      }
    }
  }
?>
