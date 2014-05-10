<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('peer.SocketHandle');

  /**
   * Instances of this Sockets class hold the read, write and except
   * arrays of Socket instances necessary for passing to `select`.
   *
   * @see   xp://peer.Socket#select
   * @see   php://socket_select
   * @see   php://stream_select
   */
  class Sockets extends Object {
    public $handles= array(NULL, NULL, NULL);
    protected $sockets= array();

    /**
     * Creates a new instances
     *
     * @param  var read either an array of peer.SocketHandles, or NULL
     * @param  var write either an array of peer.SocketHandles, or NULL
     * @param  var except either an array of peer.SocketHandles, or NULL
     */
    public function __construct($read= NULL, $write= NULL, $except= NULL) {
      $this->setSockets(0, $read);
      $this->setSockets(1, $write);
      $this->setSockets(2, $except);
    }

    /**
     * Helper: Set handles
     *
     * @param  int n
     * @param  var arg either an array of peer.SocketHandles, or NULL
     */
    protected function setSockets($n, $arg) {
      if (NULL === $arg) {
        $this->handles[$n]= NULL;
      } else {
        $this->handles[$n]= array();
        foreach ($arg as $sock) {
          $handle= cast($sock, 'peer.SocketHandle')->getHandle();
          $this->handles[$n][]= $handle;
          $this->sockets[(int)$handle]= $sock;
        }
      }
    }

    /**
     * Helper: Get sockets
     *
     * @param  int n
     * @return peer.Socket[]
     */
    protected function getSockets($n) {
      $r= array();
      if (NULL !== $this->handles[$n]) {
        foreach ($this->handles[$n] as $handle) {
          $r[]= $this->sockets[(int)$handle];
        }
      }
      return $r;
    }

    /**
     * Helper: Get read sockets
     *
     * @param  int n
     * @return peer.SocketHandle[]
     */
    public function read() {
      return $this->getSockets(0);
    }

    /**
     * Helper: Get write sockets
     *
     * @param  int n
     * @return peer.SocketHandle[]
     */
    public function write() {
      return $this->getSockets(1);
    }

    /**
     * Helper: Get except sockets
     *
     * @param  int n
     * @return peer.SocketHandle[]
     */
    public function except() {
      return $this->getSockets(2);
    }
  }
?>