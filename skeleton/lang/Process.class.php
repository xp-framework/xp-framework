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
   *   $p= &new Process('uptime');
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
    var
      $in     = NULL,
      $out    = NULL,
      $err    = NULL,
      $exitv  = -1;
      
    var
      $_proc  = NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string command
     * @param   mixed* arguments
     * @throws  io.IOException in case the command could not be executed
     */
    function __construct() {
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
        throw(new IOException('Could not execute "'.$cmd.'"'));
        return;
      }

      // Assign in, out and err members
      $this->in= &new File($pipes[0]);
      $this->out= &new File($pipes[1]);
      $this->err= &new File($pipes[2]);
    }
    
    /**
     * Get error stream
     *
     * @access  public
     * @return  &io.File STDERR
     */
    function &getErrorStream() {
      return $this->err;
    }

    /**
     * Get input stream
     *
     * @access  public
     * @return  &io.File STDIN
     */
    function &getInputStream() {
      return $this->in;
    }
    
    /**
     * Get output stream
     *
     * @access  public
     * @return  &io.File STDOUT
     */
    function &getOutputStream() {
      return $this->out;
    }
    
    /**
     * Returns the exit value for the process
     *
     * @access  public
     * @return  int
     */
    function exitValue() {
      return $this->exitv;
    }
    
    /**
     * Close this process
     *
     * @access  public
     * @return  int exit value of process
     */
    function close() {
      $this->in->close();
      $this->out->close();
      $this->err->close();
      $this->exitv= proc_close($this->_proc);
      return $this->exitv;
    }
  }
?>
