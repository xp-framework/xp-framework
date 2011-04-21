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
     * Retrieve size of address in bits
     *
     * @return  int
     */
    public function sizeInBits();
  }
?>
