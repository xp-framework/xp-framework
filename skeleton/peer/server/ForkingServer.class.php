<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('peer.server.Server', 'lang.RuntimeError');

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
     * @access  public
     */
    function service() {
      if (!$this->socket->isConnected()) return FALSE;
      
      while ($m= &$this->socket->accept()) {

        // Have connection, fork child
        $pid= pcntl_fork();
        if (-1 == $pid) {       // Woops?
          return throw(new RuntimeError('Could not fork'));
        } else if ($pid) {      // Parent
          while (pcntl_waitpid(-1, $status, WNOHANG)) { }
          $m->close();
        } else {                // Child
          $this->notify(new ConnectionEvent(EVENT_CONNECTED, $m));

          // Loop
          do {
            try(); {
              if (NULL === ($data= $m->readBinary())) break;
            } if (catch('IOException', $e)) {
              $this->notify(new ConnectionEvent(EVENT_ERROR, $m, $e));
              break;
            }

            // Notify listeners
            $this->notify(new ConnectionEvent(EVENT_DATA, $m, $data));

          } while (!$m->eof());

          $this->notify(new ConnectionEvent(EVENT_DISCONNECTED, $m));
          
          // Exit out of child
          exit();
        }
      }
    }
  }
?>
