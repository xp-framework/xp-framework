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
      if (!is_int($netmask) || $netmask < 0 || $netmask > 32)
        throw new FormatException('Netmask must be integer, between 0 and 32');

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

    /**
     * Get base / network IP
     *
     * @return  peer.InetAddress
     */
    public function getNetworkAddress() {
      return $this->address;
    }

    public function getFirstAddress() {
      return $this->address->next();
    }


  }
?>
