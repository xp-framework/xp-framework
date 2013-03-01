<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_framework.unittest.peer.ftp';

  uses(
    'util.cmd.Console',
    'util.log.Logger',
    'util.log.FileAppender',
    'peer.server.Server',
    'peer.ftp.server.FtpProtocol',
    'peer.ftp.server.storage.FilesystemStorage'
  );
  
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
  class net�xp_framework�unittest�peer�ftp�TestingServer extends Object {
    const FTPROOT= 'net.xp_framework.unittest.peer.ftp.ftproot';

    /**
     * Start server
     *
     * @param   string[] args
     */
    public static function main(array $args) {
      $cl= ClassLoader::getDefault()->findPackage(self::FTPROOT);
      $stor= new FilesystemStorage($cl->path.DIRECTORY_SEPARATOR.strtr(self::FTPROOT, '.', DIRECTORY_SEPARATOR));

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
      } catch (Throwable $e) {
        Console::writeLine('- ', $e->getMessage());
      }
    }
  }
?>
