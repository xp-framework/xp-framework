<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */

  uses('io.streams.OutputStream', 'peer.Socket');

  /**
   * OutputStream that reads from a socket
   *
   * @purpose  OutputStream implementation
   */
  class SocketOutputStream extends Object implements OutputStream {
    protected
      $socket= NULL;
    
    /**
     * Constructor
     *
     * @param   peer.Socket socket
     */
    public function __construct(Socket $socket) {
      $this->socket= $socket;
      $this->socket->isConnected() || $this->socket->connect();
    }


    /**
     * Write a string
     *
     * @param   var arg
     */
    public function write($arg) {
      $this->socket->write($arg);
    }

    /**
     * Flush this buffer
     *
     */
    public function flush() {
      // NOOP, sockets cannot be flushed
    }

    /**
     * Close this buffer
     *
     */
    public function close() {
      $this->socket->close();
    }

    /**
     * Creates a string representation of this output strean
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'<'.$this->socket->toString().'>';
    }
  }
?>
