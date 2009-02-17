<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.streams.OutputStream', 'io.IOException');

  /**
   * Output stream that writes to one of the "stdout", "stderr", "output"
   * channels provided as PHP input/output streams.
   *
   * @test     xp://net.xp_framework.unittest.io.streams.ChannelStreamTest
   * @see      php://wrappers
   * @see      xp://io.streams.ChannelInputStream
   * @purpose  Outputstream implementation
   */
  class ChannelOutputStream extends Object implements OutputStream {
    protected
      $name = NULL,
      $fd   = NULL;
    
    /**
     * Constructor
     *
     * @param   string name
     */
    public function __construct($name) {
      static $allowed= array('stdout', 'stderr', 'output');

      if (!in_array($name, $allowed) || !($this->fd= fopen('php://'.$name, 'wb'))) {
        throw new IOException('Could not open '.$name.' channel for writing');
      }
      $this->name= $name;
    }

    /**
     * Write a string
     *
     * @param   mixed arg
     */
    public function write($arg) { 
      if (FALSE === fwrite($this->fd, $arg)) {
        throw new IOException('Could not write '.strlen($arg).' bytes to '.$this->name.' channel');
      }
    }

    /**
     * Flush this stream.
     *
     */
    public function flush() {
      fflush($this->fd);
    }

    /**
     * Close this stream
     *
     */
    public function close() {
      fclose($this->fd);
    }

    /**
     * Creates a string representation of this input stream
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'(channel='.$this->name.')';
    }
  }
?>
