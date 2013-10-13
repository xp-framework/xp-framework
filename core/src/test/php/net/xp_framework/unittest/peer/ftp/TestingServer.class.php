<?php namespace net\xp_framework\unittest\peer\ftp;



use util\cmd\Console;
use util\log\Logger;
use util\log\FileAppender;
use peer\server\Server;
use peer\ftp\server\FtpProtocol;


/**
 * FTP Server used by IntegrationTest. 
 *
 * Specifics
 * ~~~~~~~~~
 * <ul>
 *   <li>Server listens on a free port @ 127.0.0.1</li>
 *   <li>Authentication requires "test" / "test" as credentials</li>
 *   <li>Storage is inside an "ftproot" subdirectory of this directory</li>
 *   <li>Server can be shut down by issuing the "SHUTDOWN" command</li>
 *   <li>On startup success, "+ Service (IP):(PORT)" is written to standard out</li>
 *   <li>On shutdown, "+ Done" is written to standard out</li>
 *   <li>On errors during any phase, "- " and the exception message are written</li>
 * </ul>
 *
 * @see   xp://net.xp_framework.unittest.peer.ftp.IntegrationTest
 */
class TestingServer extends \lang\Object {
  const FTPROOT= 'net.xp_framework.unittest.peer.ftp.ftproot';

  /**
   * Start server
   *
   * @param   string[] args
   */
  public static function main(array $args) {
    $stor= new TestingStorage();
    $stor->add(new TestingCollection('/', $stor));
    $stor->add(new TestingCollection('/.trash', $stor));
    $stor->add(new TestingElement('/.trash/do-not-remove.txt', $stor));
    $stor->add(new TestingCollection('/htdocs', $stor));
    $stor->add(new TestingElement('/htdocs/file with whitespaces.html', $stor));
    $stor->add(new TestingElement('/htdocs/index.html', $stor, "<html/>\n"));
    $stor->add(new TestingCollection('/outer', $stor));
    $stor->add(new TestingCollection('/outer/inner', $stor));
    $stor->add(new TestingElement('/outer/inner/index.html', $stor));

    $auth= newinstance('lang.Object', array(), '{
      public function authenticate($user, $password) {
        return ("testtest" == $user.$password);
      }
    }');

    $protocol= newinstance('peer.ftp.server.FtpProtocol', array($stor, $auth), '{
      public function onShutdown($socket, $params) {
        $this->answer($socket, 200, "Shutting down");
        $this->server->terminate= TRUE;
      }
    }');

    isset($args[0]) && $protocol->setTrace(Logger::getInstance()
      ->getCategory()
      ->withAppender(new FileAppender($args[0]))
    );
    
    $s= new Server('127.0.0.1', 0);
    try {
      $s->setProtocol($protocol);
      $s->init();
      Console::writeLinef('+ Service %s:%d', $s->socket->host, $s->socket->port);
      $s->service();
      Console::writeLine('+ Done');
    } catch (\lang\Throwable $e) {
      Console::writeLine('- ', $e->getMessage());
    }
  }
}
