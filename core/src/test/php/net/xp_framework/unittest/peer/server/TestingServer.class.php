<?php namespace net\xp_framework\unittest\peer\server;



use util\cmd\Console;
use peer\server\Server;
use peer\server\ServerProtocol;


/**
 * Socket server used by ServerTest. 
 *
 * Process interaction is performed by messages this server prints to
 * standard out:
 * <ul>
 *   <li>Server listens on a free port @ 127.0.0.1</li>
 *   <li>On startup success, "+ Service (IP):(PORT)" is written</li>
 *   <li>On shutdown, "+ Done" is written</li>
 *   <li>On errors during any phase, "- " and the exception message are written</li>
 * </ul>
 *
 * @see   xp://net.xp_framework.unittest.peer.server.AbstractServerTest
 */
class TestingServer extends \lang\Object {

  /**
   * Start server
   *
   * @param   string[] args
   */
  public static function main(array $args) {
    $s= new Server('127.0.0.1', 0);
    try {
      $s->setProtocol(\lang\XPClass::forName($args[0])->newInstance());
      $s->init();
      Console::writeLinef('+ Service %s:%d', $s->socket->host, $s->socket->port);
      $s->service();
      Console::writeLine('+ Done');
    } catch (\lang\Throwable $e) {
      Console::writeLine('- ', $e->getMessage());
    }
  }
}
