<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Abstract base for Inet address
   *
   * @purpose  Common ancestor for IPv4 and IPv6
   */
  abstract class InetAddress extends Object {
    protected
      $addr = NULL;

    /**
     * Retrieve "human-readable" address
     *
     * @return  string
     */
    public abstract function getAddress();
    
    /**
     * Check whether this address is a loopback address
     *
     * @return  bool
     */
    public abstract function isLoopback();
    
    /**
     * Determine whether this address is in the given network.
     *
     * @param   string net
     * @return  bool
     * @throws  lang.FormatException in case net has invalid format
     */
    public abstract function inSubnet($net);    
  }
?>
