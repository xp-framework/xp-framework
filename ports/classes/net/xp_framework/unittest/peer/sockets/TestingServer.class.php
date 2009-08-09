<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_framework.unittest.peer.sockets';

  uses(
    'util.cmd.Console',
    'peer.server.Server',
    'peer.server.ServerProtocol'
  );
  
  /**
   * Socket server used by SocketTest. Implements a simple line-based
   * protocol with the following commands:
   *
   * <ul>
   *   <li>ECHO: Echoes anything after the command</li>
   *   <li>HALT: Shuts down the server</li>
   *   <li>On startup success, "+ Service" is written to standard out</li>
   *   <li>On shutdown, "+ Done" is written to standard out</li>
   *   <li>On errors during any phase, "- " and the exception message are written</li>
   * </ul>
   *
   * @see   xp://net.xp_framework.unittest.peer.sockets.SocketTest
   */
  class net·xp_framework·unittest·peer·sockets·TestingServer extends Object {

    /**
     * Start server
     *
     * @param   string[] args
     */
    public static function main(array $args) {
      $protocol= newinstance('peer.server.ServerProtocol', array(), '{
        public function initialize() { }
        public function handleDisconnect($socket) { }
        public function handleError($socket, $e) { }
        public function handleConnect($socket) { }
        
        public function handleData($socket) {
          $cmd= $socket->readLine();
          switch (substr($cmd, 0, 4)) {
            case "ECHO": $socket->write("+ECHO ".substr($cmd, 5)."\n"); break;
            case "HALT": $socket->write("+HALT\n"); $this->server->terminate= TRUE; break;
          }
        }
      }');
      
      $s= new Server($args[0], $args[1]);
      try {
        $s->setProtocol($protocol);
        $s->init();
        Console::writeLine('+ Service');
        $s->service();
        Console::writeLine('+ Done');
      } catch (Throwable $e) {
        Console::writeLine('- ', $e->getMessage());
      }
    }
  }
?>
