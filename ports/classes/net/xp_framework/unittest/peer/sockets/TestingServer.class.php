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
   * <ul>
   *   <li>
   *     ECHO [DATA]: Echoes data following the command, terminating
   *     it by a "\n" separator.
   *   </li>
   *   <li>
   *     LINE [N] [S]: Prints N lines with separator(s) S (urlencoded)
   *     followed by a "LINE ." with "\n" separator. For example, the 
   *     command "LINE 5 %0A" prints five lines with "\n" (and the last
   *     line).
   *   </li>
   *   <li>
   *     CLOS: Closes communications socket without sending any prior
   *     notice. Can be used to simulate behaviour when connection is
   *     closed by foreign host.
   *   </li>
   *   <li>
   *     HALT: Sends "+HALT" to the client and then shuts down the 
   *     server immediately.
   *   </li>
   * </ul>
   *
   * Process interaction is performed by messages this server prints to
   * standard out:
   * <ul>
   *   <li>On startup success, "+ Service" is written</li>
   *   <li>On shutdown, "+ Done" is written</li>
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
            case "ECHO": {
              $socket->write("+ECHO ".substr($cmd, 5)."\n"); 
              break;
            }
            case "LINE": {
              sscanf(substr($cmd, 5), "%d %s", $l, $sep);
              for ($i= 0, $sbytes= urldecode($sep); $i < $l; $i++) {
                $socket->write("+LINE ".$i.$sbytes); 
              }
              $socket->write("+LINE .\n");
              break;
            }
            case "CLOS": {
              $socket->close(); 
              break;
            }
            case "HALT": {
              $socket->write("+HALT\n"); 
              $this->server->terminate= TRUE; 
              break;
            }
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
