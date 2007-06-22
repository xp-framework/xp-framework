<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.server.ServerProtocol');

  /**
   * HTTP protocol implementation
   *
   * @purpose  Protocol
   */
  class SessionProtocol extends Object implements ServerProtocol {

    /**
     * Initialize Protocol
     *
     * @return  bool
     */
    public function initialize() {
      // Intentionally empty
    }

    /**
     * Handle client connect
     *
     * @param   peer.Socket socket
     */
    public function handleConnect($socket) {
      // Intentionally empty
    }

    /**
     * Handle client disconnect
     *
     * @param   peer.Socket socket
     */
    public function handleDisconnect($socket) {
      $socket && $socket->close();
    }
    
    /**
     * Supply persistence handler
     *
     * @param   persist.SessionPersistence persist
     */
    public function setPersistence($persist) {
      $this->persist= array('@@' => $persist);
      foreach ($persist->getClass()->getMethods() as $method) {
        if (!$method->hasAnnotation('command')) continue;
        
        $a= $method->getAnnotation('command');
        $this->persist[$a['name']]= array($method, $a['args']);
      }
      $persist->server= $this->server;
    }
  
    /**
     * Handle client data
     *
     * @param   peer.Socket socket
     * @return  mixed
     */
    public function handleData($socket) {
      try {
        while (FALSE === ($p= strpos($input, "\n"))) {
          if (NULL === ($buf= $socket->readBinary(1024))) {
            // EOF
            return $socket->close();
          }
          $input.= $buf;
        }
      } catch (IOException $e) {
        // Ignore Console::$err->writeLine($e);
        return $socket->close();
      }
      
      Console::writeLine('>>> ', addcslashes($input, "\0..\17"));
      $command= substr($input, 0, $p= strpos($input, ' '));
      if (!isset($this->persist[$command])) {
        Console::$err->writeLine('*** Unknown command `', $command, '`');
        $socket->write("+OK\n"); // -ERR command `".$command."` not understood\n");
        return;
      }

      // Invoke correct handler
      $args= sscanf(substr($input, $p+ 1), $this->persist[$command][1][0]);
      try {
        $return= $this->persist[$command][0]->invoke(
          $this->persist['@@'], 
          $args
        );
      } catch (Throwable $e) {
        Console::$err->writeLine('*** Handling ', $command, '~', $e);
        $socket->write("-ERR ".$e->getMessage()."\n");
        return;
      }
      
      Console::writeLine('<<< ', addcslashes($return, "\0..\17"));
      
      if (NULL === $return) {
        $socket->write("-NOKEY\n");
      } else {
        $socket->write("+OK ".$return."\n");
      }
    }

    /**
     * Handle I/O error
     *
     * @param   peer.Socket socket
     * @param   lang.XPException e
     */
    public function handleError($socket, $e) {
      Console::$err->writeLine('* ', $socket->host, '~', $e);
      $socket->close();
    }
  }
?>
