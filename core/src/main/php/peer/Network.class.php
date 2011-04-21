<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Network class
   *
   * @purpose   Represent IP network
   */
  class Network extends Object {

    /**
     * Constructor
     *
     * @param   peer.InetAddress addr
     * @param   int netmask
     */
    public function __construct(InetAddress $addr, $netmask) {
      $this->address= $addr;
      $this->netmask= $netmask;
    }

    /**
     * Return address as string
     *
     * @return  string
     */
    public function getAddress() {
      return $this->address->getAddress().'/'.$this->netmask;
    }


  }
?>
