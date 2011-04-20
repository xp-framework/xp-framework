<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.InetAddress');

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
     * Retrieve human-readable form
     *
     * @return  string
     */
    public function getAddress() {
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
    public function inSubnet($net) {
      list ($addr, $mask)= explode('/', $net);
      if (empty($mask) || empty($addr) || $mask < 0 || $mask > 32) throw new FormatException('Invalid subnet notation given: use "host/mask"');
      $addrn= self::ip2long($addr);
      
      return $this->addr >> (32 - $mask) == $addrn >> (32 - $mask);
    }
  }
?>
