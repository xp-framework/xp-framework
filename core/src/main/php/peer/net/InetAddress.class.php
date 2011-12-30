<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Interface Inet address
   *
   * @purpose  Common ancestor for IPv4 and IPv6
   */
  interface InetAddress {

    /**
     * Retrieve "human-readable" address
     *
     * @return  string
     */
    public function asString();
    
    /**
     * Check whether this address is a loopback address
     *
     * @return  bool
     */
    public function isLoopback();
    
    /**
     * Determine whether this address is in the given network.
     *
     * @param   string net
     * @return  bool
     * @throws  lang.FormatException in case net has invalid format
     */
    public function inSubnet(Network $net);

    /**
     * Create a subnet of this address, with the specified size.
     *
     * @param   int subnetSize
     * @return  Network
     * @throws  lang.IllegalArgumentException in case the subnetSize is not correct
     */
    public function createSubnet($subnetSize);
    
    /**
     * Retrieve size of address in bits
     *
     * @return  int
     */
    public function sizeInBits();

    /**
     * Retrieve reversed notation for reverse DNS lookups
     *
     * @return  string
     */
    public function reversedNotation();
  }
?>
