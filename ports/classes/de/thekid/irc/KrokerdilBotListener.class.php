<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'peer.irc.IRCConnectionListener', 
    'peer.irc.IRCColor',
    'io.File'
  );

  /**
   * Krokerdil Bot
   *
   * @see      xp://peer.irc.IRCConnectionListener
   * @purpose  IRC Bot
   */
  class KrokerdilBotListener extends IRCConnectionListener {
  
    /**
     * Constructor
     *
     * @access  public
     */
    function __construct() {
      parent::__construct();
      
      // Load swears
      $this->swears= array();
      $f= &new File('schimpfwoerter.txt');
      try(); {
        $f->open(FILE_MODE_READ);
        while (!$f->eof()) {
          $this->swears[]= $f->readLine();
        }
      } if (catch('IOException', $e)) {
        $e->printStackTrace();
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
      $connection->join('schlund', 'bofh007');
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
      $connection->join($channel);
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
        ### MEHR VERSCHIEDENE MESSAGES UND NICK-"NICKS" EINBAUEN ###
        $connection->sendMessage($channel, 'Sieh einer an, %s ist auch da', $nick);
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
      ### MEHR VERSCHIEDENE MESSAGES UND NICK-"NICKS" EINBAUEN ###
      $connection->sendAction($channel, 'findet es schade, dass %s uns verlässt; naja, %s ist wohl Grund genug', $nick, $message);
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
      if (sscanf($message, "!%s %[^\r]", $command, $params)) {
        switch (strtolower($command)) {
          case 'bite':
            $connection->sendAction($target, 'beißt %s', $params);
            break;
            
          case 'beep':
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
            $connection->sendMessage($target, '%s ist ein %s', $params, $this->swears[rand(0, sizeof($this->swears)- 1)]);
            break;

          case 'addswear':
            if (in_array($params, $this->swears)) {
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
            $this->swears[]= $params;
            
            // Also update the swears file
            $f= &new File('schimpfwoerter.txt');
            try(); {
              $f->open(FILE_MODE_APPEND);
              $f->write($params."\n");
              $f->close();
            } if (catch('IOException', $e)) {
              $connection->sendMessage($target, '! '.$e->getMessage());
              break;
            }
            $connection->sendAction($target, 'hat jetzt %d Schimpfwörter', sizeof($this->swears));
            break;              

          case 'ascii':
            $connection->sendMessage($target, 'ASCII #%d = %s', $params, chr($params));
            break;
        }
        return;
      }
      
      // Ignore
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
      $connection->writeln("NOTICE %s :\1VERSION Stoopidbot 0.3\1", $nick);
    }
  }
?>
