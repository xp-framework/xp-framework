<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('peer.server.Server');

  /**
   * (Insert class' description here)
   *
   * @ext      pcntl
   * @see      reference
   * @purpose  purpose
   */
  class ForkingServer extends Server {
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function service() {
      if (!$this->socket->isConnected()) return FALSE;
      
      while ($m= &$this->socket->accept()) {

        // Have connection, fork child
        $pid= pcntl_fork();
        if (-1 == $pid) {       // Woops?
          return throw(new RuntimeException('Could not fork'));
        } else if ($pid) {      // Parent
          while (pcntl_waitpid(-1, $status, WNOHANG)) { }
        } else {                // Child
          $this->notify(new ConnectionEvent(EVENT_CONNECTED, $m));

          // Loop
          do {
            try(); {
              if (NULL === ($data= $m->read())) break;
            } if (catch('IOException', $e)) {
              $this->notify(new ConnectionEvent(EVENT_ERROR, $m, $e));
              break;
            }

            // Notify listeners
            $this->notify(new ConnectionEvent(EVENT_DATA, $m, $data));

          } while (!$m->eof());

          $this->notify(new ConnectionEvent(EVENT_DISCONNECTED, $m));
          
          // Close communications and exit out of child
          $m->close();
          exit();
        }
      }
    }
  }
?>
