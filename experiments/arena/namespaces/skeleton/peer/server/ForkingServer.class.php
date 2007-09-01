<?php
/* This class is part of the XP framework
 *
 * $Id: ForkingServer.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace peer::server;

  ::uses('peer.server.Server', 'lang.RuntimeError');

  /**
   * Forking TCP/IP Server
   *
   * @ext      pcntl
   * @see      xp://peer.server.Server
   * @purpose  TCP/IP Server
   */
  class ForkingServer extends Server {
    
    /**
     * Service
     *
     */
    public function service() {
      if (!$this->socket->isConnected()) return FALSE;
      
      $tcp= getprotobyname('tcp');
      while (!$this->terminate) {
        try {
          $m= $this->socket->accept();
        } catch (io::IOException $e) {
          $this->shutdown();
          break;
        }
        if (!$m) continue;

        // Have connection, fork child
        $pid= pcntl_fork();
        if (-1 == $pid) {       // Woops?
          throw(new lang::RuntimeError('Could not fork'));
        } else if ($pid) {      // Parent

          // Close own copy of message socket
          $m->close();
          delete($m);
          
          // Use waitpid w/ NOHANG to avoid zombies hanging around
          while (pcntl_waitpid(-1, $status, WNOHANG)) { }
        } else {                // Child
          // Handle initialization of protocol. This is called once for 
          // every new child created
          $this->protocol->initialize();

          $this->tcpnodelay && $m->setOption($tcp, TCP_NODELAY, TRUE);
          $this->protocol->handleConnect($m);

          // Loop
          do {
            try {
              $this->protocol->handleData($m);
            } catch (io::IOException $e) {
              $this->protocol->handleError($m, $e);
              break;
            }

          } while (!$m->eof());

          $this->protocol->handleDisconnect($m);
          $m->close();

          // Exit out of child
          exit();
        }
      }
    }
  }
?>
