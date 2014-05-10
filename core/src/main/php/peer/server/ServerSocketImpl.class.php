<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('lang.Closeable');

  /**
   * Abstract server socket implementation
   */
  abstract class ServerSocketImpl extends Object implements Closeable{
    protected $handle= NULL;
    protected $host;
    protected $port;

    public abstract function __construct($domain, $type, $protocol);

    public abstract function bind($host, $port, $backlog= 10, $reuse= TRUE);

    /**
     * Returns the underlying socket host
     *
     * @return  string
     */
    public function host() {
      return $this->host;
    }

    /**
     * Returns the underlying socket port
     *
     * @return  int
     */
    public function port() {
      return $this->port;
    }

    /**
     * Returns the underlying socket handle
     *
     * @return  var
     */
    public function handle() {
      return $this->handle;
    }

    /**
     * Returns whether this socket is connected
     *
     * @return  bool
     */
    public function isConnected() {
      return NULL !== $this->handle;
    }

    public abstract function accept();

    /**
     * Select
     *
     * @param   peer.Sockets s
     * @param   float timeout default NULL Timeout value in seconds (e.g. 0.5)
     * @return  int
     * @throws  peer.SocketException in case of failure
     */
    public abstract function select(Sockets $s, $timeout= NULL);

    public function __destruct() {
      $this->close();
    }
  }
?>