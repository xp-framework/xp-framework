<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Connection listener
   *
   * @see      xp://peer.irc.IRCConnection#addListener
   * @purpose  Abstract base class
   */
  class IRCConnectionListener extends Object {

    /**
     * Callback for Pings. Note that the PING has already been answered
     * when this method is called, so you won't have to send a PONG 
     * yourself.
     *
     * You might want to use this method in IRC-bots to accomplish the
     * task of deliberately being able to perform an action without any
     * other action having taken place (e.g., maintenance, reload config,
     * ...)
     *
     * @param   peer.irc.IRCConnection connection
     * @param   string data
     */
    public function onPings($connection, $data) { }

    /**
     * Callback for when a connection to the IRC server has been 
     * established. This method is called *after* a connecting was
     * successful.
     *
     * @param   peer.irc.IRCConnection connection
     * @param   string server
     * @param   int port
     */
    public function onConnect($connection, $server, $port) { }

    /**
     * Callback for when a connection to the IRC server has been 
     * closed. This method is called *before* the connection is actually
     * dropped; thus making it possible to say goodbye. You cannot do
     * anything to prevent disconnection, though.
     *
     * @param   peer.irc.IRCConnection connection
     * @param   string server
     * @param   int port
     */
    public function onDisconnect($connection, $server, $port) { }
    
    /**
     * Callback for server message MOTDSTART (375)
     *
     * @param   peer.irc.IRCConnection connection
     * @param   string server
     * @param   string target whom the message is for
     * @param   string data
     */
    public function onMOTDStart($connection, $server, $target, $data) { }

    /**
     * Callback for server message MOTD (372)
     *
     * @param   peer.irc.IRCConnection connection
     * @param   string server
     * @param   string target whom the message is for
     * @param   string data
     */
    public function onMOTD($connection, $server, $target, $data) { }

    /**
     * Callback for server message REPLY_ENDOFMOTD (376)
     *
     * @param   peer.irc.IRCConnection connection
     * @param   string server
     * @param   string target whom the message is for
     * @param   string data
     */
    public function onEndOfMOTD($connection, $server, $target, $data) { }
    
    /**
     * Callback for all other server messages
     *
     * @param   peer.irc.IRCConnection connection
     * @param   string server
     * @param   int code one of the IRC_* constants from peer.irc.IRCConstants
     * @param   string target whom the message is for
     * @param   string data
     */
    public function onServerMessage($connection, $server, $code, $target, $data) { }

    /**
     * Callback for invitations. Note: Due to the limitations of the INVITE
     * command, you won't be able to join password-protected channels unless
     * you know their password!
     *
     * Example: Join if we're invited:
     * <code>
     *   $connection->join($channel);
     * </code>
     *
     * @param   peer.irc.IRCConnection connection
     * @param   string nick sending the invitation
     * @param   string who who is invited
     * @param   string channel invitation is for
     */
    public function onInvite($connection, $nick, $who, $channel) { }

    /**
     * Callback for kicks
     *
     * Example (identifying being kicked):
     * <code>
     *   if (strcasecmp($who, $connection->user->getNick()) == 0) {
     *     // ... I was kicked ...
     *   }
     * </code>
     *
     * @param   peer.irc.IRCConnection connection
     * @param   string channel the channel the user was kicked from
     * @param   string nick that initiated the kick
     * @param   string who who was kicked
     * @param   string reason what reason the user was kicked for
     */
    public function onKicks($connection, $channel, $nick, $who, $reason) { }

    /**
     * Callback for quits
     *
     * @param   peer.irc.IRCConnection connection
     * @param   string channel the channel the user quit from
     * @param   string nick who quit
     * @param   string reason what reason the user supplied for quitting
     */
    public function onQuits($connection, $channel, $nick, $reason) { }

    /**
     * Callback for nick changes
     *
     * @param   peer.irc.IRCConnection connection
     * @param   string nick the old nick
     * @param   string new the new nick
     */
    public function onNickChanges($connection, $nick, $new) { }

    /**
     * Callback for joins
     *
     * Example (welcome users)
     * <code>
     *   // Send it to the channel so everybody knows
     *   $connection->sendMessage($channel, 'Welcome %s', $nick);
     *
     *   // Send it to the joinee privately
     *   $connection->sendMessage($nick, 'Welcome!');
     * </code>
     *
     * @param   peer.irc.IRCConnection connection
     * @param   string channel which channel was joined
     * @param   string nick who joined
     */
    public function onJoins($connection, $channel, $nick) { }

    /**
     * Callback for parts
     *
     * @param   peer.irc.IRCConnection connection
     * @param   string channel which channel was part
     * @param   string nick who part
     * @param   string message the part message, if any
     */
    public function onParts($connection, $channel, $nick, $message) { }
    
    /**
     * Callback for mode changes
     *
     * @param   peer.irc.IRCConnection connection
     * @param   string nick who initiated the mode change
     * @param   string target what the mode setting is for (e.g. +k #channel, +i user)
     * @param   string mode the mode including a + or - as its first letter
     * @param   string params additional parameters
     */
    public function onModeChanges($connection, $nick, $target, $mode, $params) { }
  
    /**
     * Callback for private messages
     *
     * Example (implementing "commands"):
     * <code>
     *   if (sscanf($message, "!%s %[^\r]", $command, $params)) {
     *     switch (strtolower($command)) {
     *       case 'status':
     *         // ...
     *     }
     *   }
     * </code>
     *
     * @param   peer.irc.IRCConnection connection
     * @param   string nick
     * @param   string target
     * @param   string message
     */
    public function onPrivateMessage($connection, $nick, $target, $message) { }

    /**
     * Callback for topic changes
     *
     * @param   peer.irc.IRCConnection connection
     * @param   string nick who changed the topic
     * @param   string channel what channel the topic was changed for
     * @param   string topic the new topic
     */
    public function onTopic($connection, $nick, $channel, $topic) { }

    /**
     * Callback for notices
     *
     * @param   peer.irc.IRCConnection connection
     * @param   string nick
     * @param   string target
     * @param   string message
     */
    public function onNotice($connection, $nick, $target, $message) { }

    /**
     * Callback for actions. Actions are when somebody writes /me ...
     * in their IRC window.
     *
     * Example (annoying:)):
     * <code>
     *   $connection->sendAction($target, 'imitates %s and %s, too', $nick, $params);
     * </code>
     *
     * @param   peer.irc.IRCConnection connection
     * @param   string nick who initiated the action
     * @param   string target where action was initiated
     * @param   string action what actually happened (e.g. "looks around")
     */
    public function onAction($connection, $nick, $target, $action) { }

    /**
     * Callback for CTCP VERSION
     *
     * @param   peer.irc.IRCConnection connection
     * @param   string nick nick requesting version
     * @param   string target where version was requested
     * @param   string params additional parameters
     */
    public function onVersion($connection, $nick, $target, $params) { }

    /**
     * Callback for CTCP USERINFO
     *
     * @param   peer.irc.IRCConnection connection
     * @param   string nick nick requesting user information
     * @param   string target where user information was requested
     * @param   string params additional parameters
     */
    public function onUserInfo($connection, $nick, $target, $params) { }

    /**
     * Callback for CTCP CLIENTINFO
     *
     * @param   peer.irc.IRCConnection connection
     * @param   string nick nick requesting client information
     * @param   string target where client information was requested
     * @param   string params additional parameters
     */
    public function onClientInfo($connection, $nick, $target, $params) { }

    /**
     * Callback for CTCP PING
     *
     * @param   peer.irc.IRCConnection connection
     * @param   string nick nick requesting ping
     * @param   string target where ping was requested
     * @param   string params additional parameters
     */
    public function onPing($connection, $nick, $target, $params) { }

    /**
     * Callback for CTCP TIME
     *
     * @param   peer.irc.IRCConnection connection
     * @param   string nick nick requesting time
     * @param   string target where time was requested
     * @param   string params additional parameters
     */
    public function onTime($connection, $nick, $target, $params) { }

    /**
     * Callback for CTCP FINGER
     *
     * @param   peer.irc.IRCConnection connection
     * @param   string nick nick requesting finger information
     * @param   string target where finger information was requested
     * @param   string params additional parameters
     */
    public function onFinger($connection, $nick, $target, $params) { }
  
  }
?>
