<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Output stream
   *
   * @see      xp://util.cmd.Console
   * @purpose  Wrapper
   */
  class ConsoleOutputStream extends Object {
    protected 
      $fd   = NULL;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   resource fd
     */
    public function __construct($fd) {
      $this->fd= $fd;
    }

    /**
     * Flush output buffer
     *
     * @model   static
     * @access  public
     */
    public function flush() {
      fflush($this->fd);
    }

    /**
     * Write a string to standard output
     *
     * @model   static
     * @access  public
     * @param   mixed* args
     */
    public function write() {
      $a= func_get_args();
      fwrite($this->fd, implode('', $a));
    }
    
    /**
     * Write a string to standard output and append a newline
     *
     * @model   static
     * @access  public
     * @param   mixed* args
     */
    public function writeLine() {
      $a= func_get_args();
      fwrite($this->fd, implode('', $a)."\n");
    }
    
    /**
     * Write a formatted string to standard output
     *
     * @model   static
     * @access  public
     * @param   string format
     * @param   mixed* args
     * @see     php://printf
     */
    public function writef() {
      $a= func_get_args();
      fwrite($this->fd, vsprintf(array_shift($a), $a));
    }

    /**
     * Write a formatted string to standard output and append a newline
     *
     * @model   static
     * @access  public
     * @param   string format
     * @param   mixed* args
     */
    public function writeLinef() {
      $a= func_get_args();
      fwrite($this->fd, vsprintf(array_shift($a), $a)."\n");
    }
  }
?>
