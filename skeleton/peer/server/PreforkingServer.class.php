<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('peer.server.Server', 'lang.RuntimeError');

  /**
   * Pre-Forking TCP/IP Server
   *
   * @ext      pcntl
   * @see      xp://peer.server.Server
   * @purpose  TCP/IP Server
   */
  class PreforkingServer extends Server {
    var
      $cat   = NULL,
      $count = 0;

    /**
     * Constructor
     *
     * @access  public
     * @param   string addr
     * @param   int port
     * @param   int count default 10 number of children to fork
     */
    function __construct($addr, $port, $count= 10) {
      parent::__construct($addr, $port);
      $this->count= $count;
    }
    
    /**
     * Set a trace for debugging
     *
     * @access  public
     * @param   &util.log.LogCategory cat
     */
    function setTrace(&$cat) { 
      $this->cat= &$cat;
    }

    /**
     * Signal handler
     *
     * @access  protected
     * @param   int sig
     */
    function handleSignal($sig) {
      $this->terminate= TRUE;
    }

    /**
     * Service
     *
     * @access  public
     */
    function service() {
      if (!$this->socket->isConnected()) return FALSE;

      // Set up signal handler so a kill -2 $pid (where $pid is the 
      // process id of the process we are running in) will cleanly shut
      // down this server. If this server is run within a thread (which
      // is recommended), a $thread->stop() will accomplish this.
      pcntl_signal(SIGINT, array(&$this, 'handleSignal'));
      
      $children= array();
      $i= 0;
      while ($i <= $this->count) {
        $pid= pcntl_fork();
        if (-1 == $pid) {       // Woops?
          return throw(new RuntimeError('Could not fork'));
        } else if ($pid) {      // Parent
          $this->cat && $this->cat->infof('Forked child #%d with pid %d', $i, $pid);
          $children[$pid]= $i;
          $i++;
        } else {                // Child
          while (!$this->terminate) {
            try(); {
              $m= &$this->socket->accept();
            } if (catch('IOException', $e)) {
              $this->cat && $this->cat->warn('Child', getmypid(), 'in accept ~', $e);
              exit(-1);
            }
            if (!$m) continue;

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

            $m->close();
            $this->notify(new ConnectionEvent(EVENT_DISCONNECTED, $m));
          }

          // Exit out of child
          exit();
        }
        if ($i < $this->count) continue;
        
        // Wait until we are supposed to terminate. This condition variable
        // is set to TRUE by the signal handler. Sleep a second to decrease
        // load produced. Note: sleep() is interrupted by a SIGINT, we will
        // still be able to catch the shutdown signal in realtime.
        $this->cat && $this->cat->debug('Starting main loop, children:', $children);
        while (!$this->terminate) { 
          sleep(1);
          if (($pid= pcntl_waitpid(-1, $status, WNOHANG)) <= 0) continue;

          // One of our children terminated, remove it from the process 
          // list and fork a new one
          $this->cat && $this->cat->warnf('Child %d died with exitcode %d', $pid, $status);
          unset($children[$pid]);
          $i--;
          break;
        }
      }
      
      // Terminate children
      foreach (array_keys($children) as $pid) {
        $this->cat && $this->cat->info('Terminating child', $pid);
        posix_kill($pid, SIGINT);
      }

      // Shut down ourselves
      $this->shutdown();
    }
  } implements(__FILE__, 'util.log.Traceable');
?>
