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
      if (!is_int($netmask) || $netmask < 0 || $netmask > $addr->sizeInBits())
        throw new FormatException('Netmask must be integer, between 0 and '.$addr->sizeInBits());

      $this->address= $addr;
      $this->netmask= $netmask;
    }

    /**
     * Acquire address
     *
     * @return  peer.InetAddress
     */
    public function getAddress() {
      return $this->address;
    }

    /**
     * Get netmask
     *
     * @return  int
     */
    public function getNetmask() {
      return $this->netmask;
    }

    /**
     * Return address as string
     *
     * @return  string
     */
    public function asString() {
      return $this->address->asString().'/'.$this->netmask;
    }

    /**
     * Get base / network IP
     *
     * @return  peer.InetAddress
     */
    public function getNetworkAddress() {
      return $this->address;
    }

    /**
     * Determine whether given address is part of this network
     *
     * @param   peer.InetAddress addr
     * @return  bool
     */
    public function contains(InetAddress $addr) {
      return $addr->inSubnet($this);
    }

    /**
     * Check if object is equal
     *
     * @param   lang.Object cmp
     */
    public function equals($cmp) {
      return $cmp instanceof self &&
        $cmp->netmask === $this->netmask &&
        $this->address->equals($cmp->address)
      ;
    }

    /**
     * Retrieve string representation
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'('.$this->address->asString().'/'.$this->netmask.')';
    }
  }
?>
