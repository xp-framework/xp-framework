<?php
/* This class is part of the XP framework
 *
 * $Id: PreforkingServer.class.php 7786 2006-08-21 08:32:06Z friebe $
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
      $cat          = NULL,
      $count        = 0,
      $sigs         = array(),
      $timout       = 0,
      $restart      = FALSE,
      $null         = NULL;

    /**
     * Constructor
     *
     * @access  public
     * @param   string addr
     * @param   int port
     * @param   int count default 10 number of children to fork
     * @param   int maxrequests default 1000 maxmimum # of requests per child
     */
    function __construct($addr, $port, $count= 10, $maxrequests= 1000, $timeout= 60) {
      parent::__construct($addr, $port);
      $this->count= $count;
      $this->maxrequests= $maxrequests;
      $this->timeout= $timeout;
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
      $this->cat && $this->cat->debugf('Received signal %d in pid %d', $sig, getmypid());
      
      switch ($sig) {
        case SIGINT: $this->terminate= TRUE; break;
        case SIGHUP: $this->restart= TRUE; break;
      }
    }

    /**
     * Terminate child processes
     *
     * @access  private
     * @param   array children
     */
    function _killChildren(&$children) {
      foreach ($children as $pid => $i) {
        $this->cat && $this->cat->infof('Server #%d: Terminating child #%d with pid %d', getmypid(), $i, $pid);
        posix_kill($pid, SIGINT);
        pcntl_waitpid($pid, $status, WUNTRACED);
        $this->cat && $this->cat->debugf('Server #%d: Exitcode is %d', getmypid(), $status);
        unset($children[$pid]);
      }
      
      $this->restart= FALSE;
    }

    /**
     * Handle a forked child
     *
     * @access  protected
     */
    function handleChild() {
      
      // Install child signal handler
      pcntl_signal(SIGINT, array(&$this, 'handleSignal'));

      // Handle initialization of protocol. This
      // is called once for every new child created
      if (is('util.Initializable', $this->protocol)) {
        $this->protocol->initialize();
      }
      
      $read= array($this->socket->_sock);
      $requests= 0;
      while (!$this->terminate && $requests < $this->maxrequests) {
        try(); {
        
          // Wait for incoming data. This call can be interrupted
          // by an incoming signal.
          if (FALSE === socket_select($read, $this->null, $this->null, NULL)) {
            $this->terminate || $this->cat && $this->cat->warn('Child', getmypid(), 'select failed');
            return;
          }
          
          // There is data on the socket.
          // Handle it!
          $m= &$this->socket->accept();
        } if (catch('IOException', $e)) {
          $this->cat && $this->cat->warn('Child', getmypid(), 'in accept ~', $e);
          return;
        }
        
        $this->tcpnodelay && $m->setOption($tcp, TCP_NODELAY, TRUE);
        $this->protocol->handleConnect($m);

        // Loop
        do {
          try(); {
            $this->protocol->handleData($m);
          } if (catch('IOException', $e)) {
            $this->protocol->handleError($m, $e);
            break;
          }
        } while (!$m->eof());

        $m->close();
        $this->protocol->handleDisconnect($m);
        $requests++;
        $this->cat && $this->cat->debug(
          'Child', getmypid(), 
          'requests=', $requests, 'max= ', $this->maxrequests
        );
      }
      
      if (is('util.Initializable', $this->protocol)) {
        $this->protocol->finalize();
      }
    }

    /**
     * Service
     *
     * @access  public
     */
    function service() {
      if (!$this->socket->isConnected()) return FALSE;

      $children= array();
      $i= 0;
      $tcp= getprotobyname('tcp');
      while (!$this->terminate && (sizeof($children) <= $this->count)) {
        $this->cat && $this->cat->debugf('Server #%d: Forking child %d', getmypid(), $i);
        $pid= pcntl_fork();
        if (-1 == $pid) {       // Woops?
          return throw(new RuntimeError('Could not fork'));
        } else if ($pid) {      // Parent
          $this->cat && $this->cat->infof('Server #%d: Forked child #%d with pid %d', getmypid(), $i, $pid);
          $children[$pid]= $i;
          $i++;
        } else {                // Child
          $this->handleChild();

          // Exit out of child
          exit();
        }
        if (sizeof($children) < $this->count) continue;

        // Set up signal handler so a kill -2 $pid (where $pid is the 
        // process id of the process we are running in) will cleanly shut
        // down this server. If this server is run within a thread (which
        // is recommended), a $thread->stop() will accomplish this.
        pcntl_signal(SIGINT, array(&$this, 'handleSignal'));
        pcntl_signal(SIGHUP, array(&$this, 'handleSignal'));
        
        // Wait until we are supposed to terminate. This condition variable
        // is set to iTRUE by the signal handler. Sleep a second to decrease
        // load produced. Note: sleep() is interrupted by a SIGINT, we will
        // still be able to catch the shutdown signal in realtime.
        $this->cat && $this->cat->debug('Server #'.getmypid().': Starting main loop, children:', $children);
        while (!$this->terminate) { 
          usleep(1);
          
          // If we get SIGHUP restart child processess
          if ($this->restart) {
            $this->_killChildren($children);
            break;
          }

          if (($pid= pcntl_waitpid(-1, $status, WNOHANG)) <= 0) continue;
          
          // If, meanwhile, we've been interrupted, break out of both loops
          if ($this->terminate) break 2;


          // One of our children terminated, remove it from the process 
          // list and fork a new one
          $this->cat && $this->cat->warnf('Server #%d: Child %d died with exitcode %d', getmypid(), $pid, $status);
          unset($children[$pid]);
          break;
        }
        
        // Reset signal handler so it doesn't get copied to child processes
        pcntl_signal(SIGINT, SIG_DFL);
        pcntl_signal(SIGHUP, SIG_DFL);
      }
      
      // Terminate children
      $this->_killChildren($children);

      // Shut down ourselves
      $this->shutdown();
      $this->cat && $this->cat->infof('Server #%d: Shutdown complete', getmypid());
    }
  } implements(__FILE__, 'util.log.Traceable');
?>
