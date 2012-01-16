<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.net.InetAddress');

  /**
   * IPv4 address
   *
   * @test      xp://net.xp_framework.unittest.peer.Inet4AddressTest
   * @see       php://ip2long
   * @purpose   Represent an IPv4 address
   */
  class Inet4Address extends Object implements InetAddress {
  
    /**
     * Convert IPv4 address from dotted form into a long
     *
     * @param   string ip
     * @return  int
     */
    protected static function ip2long($ip) {
      $i= 0; $addr= 0; $count= 0;
      foreach (explode('.', $ip) as $byte) {
        if (++$count > 4)
          throw new FormatException('Given IP string has more than 4 blocks: ['.$ip.']');

        if (!is_numeric($byte) || $byte < 0 || $byte > 255)
          throw new FormatException('Invalid format of ip address: ['.$ip.']');

        $addr|= ($byte << (8 * (3 - $i++)));
      }
      return $addr;
    }
    
    /**
     * Constructor
     *
     * @param   string address
     * @throws  lang.FormatException in case address is illegal
     */
    public function __construct($address) {
      $this->addr= self::ip2long($address);
    }

    /**
     * Retrieve size of ips of this kind in bits.
     *
     * @return  int
     */
    public function  sizeInBits() {
      return 32;
    }

    /**
     * Retrieve IP address notation for DNS reverse query
     *
     * @return  string
     */
    public function reversedNotation() {
      return implode('.', array_reverse(explode('.', $this->asString()))).'.in-addr.arpa';
    }

    /**
     * Retrieve human-readable form
     *
     * @return  string
     */
    public function asString() {
      return long2ip($this->addr);
    }
    
    /**
     * Determine whether address is a loopback address
     *
     * @return  bool
     */
    public function isLoopback() {
      return $this->addr >> 8 == 0x7F0000;
    }
    
    /**
     * Determine whether address is in the given subnet
     *
     * @param   string net
     * @return  bool
     * @throws  lang.FormatException in case net has invalid format
     */
    public function inSubnet(Network $net) {
      if (!$net->getAddress() instanceof self) return FALSE;
      
      $addrn= $net->getAddress()->addr;
      $mask= $net->getNetmask();
      return $this->addr >> (32 - $mask) == $addrn >> (32 - $mask);
    }
    
    /**
     * Create a subnet of this address, with the specified size.
     *
     * @param   int subnetSize
     * @return  Network
     * @throws  lang.IllegalArgumentException in case the subnetSize is not correct
     */
    public function createSubnet($subnetSize) {
      $addr= $this->addr & (0xFFFFFFFF << (32-$subnetSize));
      return new Network(new Inet4Address(long2ip($addr)), $subnetSize);
    }
    
    /**
     * Equals method
     *
     * @param   lang.Object cmp
     */
    public function equals($cmp) {
      return $cmp instanceof self && $this->addr === $cmp->addr;
    }

    /**
     * Get string representation
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'('.$this->asString().')';
    }

    /**
     * Magic string case callback
     *
     * @return  string
     */
    public function  __toString() {
      return $this->asString();
    }
  }
?>
