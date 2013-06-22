<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Socket endpoint
   *
   * @test  xp://net.xp_framework.unittest.peer.sockets.SocketEndpointTest
   * @test  xp://net.xp_framework.unittest.peer.sockets.AbstractSocketTest
   */
  class SocketEndpoint extends Object {
    protected $host= '';
    protected $port= 0;
    
    /**
     * Constructor
     *
     * @param   var host either hostname or an IP address in string or peer.net.InetAddress form
     * @param   int port
     */
    public function __construct($host, $port) {
      if ($host instanceof InetAddress) {
        $this->host= $host->asString();
      } else {
        $this->host= (string)$host;
      }
      $this->port= $port;
    }

    /**
     * Parses an instance of this object from a given string in hostname:port notation
     *
     * @param   string in
     * @return  self
     * @throws  lang.FormatException
     */
    public static function valueOf($in) {
      if (FALSE === ($p= strrpos($in, ':'))) {
        throw new FormatException('Malformed address "'.$in.'": Missing colon');
      }
      if (!is_numeric($port= substr($in, $p+ 1))) {
        throw new FormatException('Malformed address "'.$in.'": Non-numeric port');
      }
      return new self(substr($in, 0, $p), (int)$port);
    }

    /**
     * Gets host
     *
     * @return  string
     */
    public function getHost() {
      return $this->host;
    }

    /**
     * Gets port
     *
     * @return  int
     */
    public function getPort() {
      return $this->port;
    }

    /**
     * Gets address (hostname:port notation)
     *
     * @return  string
     */
    public function getAddress() {
      return $this->host.':'.$this->port;
    }

    /**
     * Returns whether a given value is equal to this instance
     *
     * @param   var cmp
     * @return  bool
     */
    public function equals($cmp) {
      return (
        $cmp instanceof self &&
        $this->host === $cmp->host &&
        $this->port === $cmp->port
      );
    }

    /**
     * Creates a hashcode for this endpoint
     *
     * @return  string
     */
    public function hashCode() {
      return $this->getAddress();
    }

    /**
     * Creates a string representation
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'('.$this->host.':'.$this->port.')';
    }
  }
?>
