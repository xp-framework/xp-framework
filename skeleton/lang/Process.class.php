<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.File');

  /**
   * Process
   *
   * Example (get uptime information on a *NIX system)
   * <code>
   *   $p= new Process('uptime');
   *   $uptime= $p->out->readLine();
   *   $p->close();
   *
   *   var_dump($uptime);
   * </code>
   *
   * @see      xp://lang.Runtime#getExecutable
   * @see      php://proc_open
   * @purpose  Execute external programs
   */
  class Process extends Object {
    public
      $in     = NULL,
      $out    = NULL,
      $err    = NULL,
      $exitv  = -1;
      
    protected
      $_proc  = NULL,
      $status = array();
      
    /**
     * Constructor
     *
     * @param   string command default NULL
     * @param   string[] arguments default []
     * @throws  io.IOException in case the command could not be executed
     */
    public function __construct($command= NULL, $arguments= array()) {
      static $spec= array(
        0 => array('pipe', 'r'),  // stdin
        1 => array('pipe', 'w'),  // stdout
        2 => array('pipe', 'w')   // stderr
      );
      
      // For `new self()` used in getProcessById()
      if (NULL === $command) return;
      
      // Build command line
      $cmd= $command.' '.implode(' ', $arguments);

      // Open process
      if (!is_resource($this->_proc= proc_open($cmd, $spec, $pipes))) {
        throw new IOException('Could not execute "'.$cmd.'"');
      }

      $this->status= proc_get_status($this->_proc);
      $this->status['exe']= realpath($command);

      // Assign in, out and err members
      $this->in= new File($pipes[0]);
      $this->out= new File($pipes[1]);
      $this->err= new File($pipes[2]);
    }
    
    /**
     * Get a process by process ID
     *
     * @param   int pid process id
     * @return  lang.Process
     * @throws  lang.IllegalStateException
     */
    public static function getProcessById($pid) {
      $self= new self();
      $self->status= array(
        'pid'       => $pid, 
        'running'   => TRUE
      );
      
      // Determine executable and command line:
      // * On Windows, use Windows Management Instrumentation API - see
      //   http://en.wikipedia.org/wiki/Windows_Management_Instrumentation
      //
      // * On systems with a /proc filesystem, use information from /proc/self
      //   See http://en.wikipedia.org/wiki/Procfs
      //
      // * Fall back to use the "_" environment variable and /bin/ps to retrieve
      //   the command line (please note unfortunately any quote signs have been 
      //   lost and it can thus be only used for display purposes)
      //
      // Note: It would be really nice to have a getmyexe() function in PHP
      // complementing getmypid().
      if (strncasecmp(PHP_OS, 'Win', 3) === 0) {
        try {
          $c= new Com('winmgmts:');
          $p= $c->get('//./root/cimv2:Win32_Process.Handle="'.$pid.'"');
          $self->status['exe']= $p->executablePath;
          $self->status['command']= $p->commandLine;
        } catch (Exception $e) {
          throw new IllegalStateException('Cannot find executable: '.$e->getMessage());
        }
      } else if (file_exists($proc= '/proc/'.$pid)) {
        $self->status['exe']= readlink($proc.'/exe');
        $self->status['command']= strtr($proc.'/cmdline', "\0", ' ');
      } else if ($_= getenv('_')) {
        $self->status['exe']= realpath($_);
        $self->status['command']= exec('ps -p '.$pid.' -ocommand');
      } else {
        throw new IllegalStateException('Cannot find executable');
      }
      $self->in= xp::null();
      $self->out= xp::null();
      $self->err= xp::null();
      return $self;
    }
    
    /**
     * Get process ID
     *
     * @return  int
     */
    public function getProcessId() {
      return $this->status['pid'];
    }
    
    /**
     * Get filename of executable
     *
     * @return  string
     */
    public function getFilename() {
      return $this->status['exe'];
    }

    /**
     * Get command line
     *
     * @return  string
     */
    public function getCommandLine() {
      return $this->status['command'];
    }
    
    /**
     * Get error stream
     *
     * @return  io.File STDERR
     */
    public function getErrorStream() {
      return $this->err;
    }

    /**
     * Get input stream
     *
     * @return  io.File STDIN
     */
    public function getInputStream() {
      return $this->in;
    }
    
    /**
     * Get output stream
     *
     * @return  io.File STDOUT
     */
    public function getOutputStream() {
      return $this->out;
    }
    
    /**
     * Returns the exit value for the process
     *
     * @return  int
     */
    public function exitValue() {
      return $this->exitv;
    }
    
    /**
     * Close this process
     *
     * @return  int exit value of process
     */
    public function close() {
      $this->in->isOpen() && $this->in->close();
      $this->out->isOpen() && $this->out->close();
      $this->err->isOpen() && $this->err->close();
      $this->exitv= proc_close($this->_proc);
      return $this->exitv;
    }
  }
?>
