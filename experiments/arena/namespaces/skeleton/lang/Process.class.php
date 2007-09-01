<?php
/* This class is part of the XP framework
 *
 * $Id: Process.class.php 9090 2007-01-03 13:57:55Z friebe $ 
 */

  namespace lang;

  ::uses('io.File');

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
   * @see      php://proc_open
   * @purpose  Execute external programs
   */
  class Process extends Object {
    public
      $in     = NULL,
      $out    = NULL,
      $err    = NULL,
      $exitv  = -1;
      
    public
      $_proc  = NULL;
      
    /**
     * Constructor
     *
     * @param   string command
     * @param   mixed* arguments
     * @throws  io.IOException in case the command could not be executed
     */
    public function __construct() {
      static $spec= array(
        0 => array('pipe', 'r'),  // stdin
        1 => array('pipe', 'w'),  // stdout
        2 => array('pipe', 'w')   // stderr
      );
      
      // Build command line
      $a= func_get_args();
      $cmd= implode(' ', $a);
      
      // Open process
      if (!is_resource($this->_proc= proc_open($cmd, $spec, $pipes))) {
        throw(new io::IOException('Could not execute "'.$cmd.'"'));
        return;
      }

      // Assign in, out and err members
      $this->in= new io::File($pipes[0]);
      $this->out= new io::File($pipes[1]);
      $this->err= new io::File($pipes[2]);
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
