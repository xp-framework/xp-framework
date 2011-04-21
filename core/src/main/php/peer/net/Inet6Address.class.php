<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.net.InetAddress');
  
  /**
   * IPv4 address
   *
   * @test      xp://net.xp_framework.unittest.peer.Inet6AddressTest
   * @see       php://ip2long
   * @purpose   Represent an IPv6 address
   */
  class Inet6Address extends Object implements InetAddress {
    
    /**
     * Constructor
     *
     * @param   string address
     */
    public function __construct($addr) {
      $addr= self::normalize($addr);
      $this->addr= pack('H*', $addr);
    }

    /**
     * Retrieve size of ips of this kind in bits.
     *
     * @return  int
     */
    public function sizeInBits() {
      return 128;
    }

    /**
     * Normalize address
     *
     * @param   string addr
     * @return  string
     */
    protected static function normalize($addr) {
      $out= '';
      $hexquads= explode(':', $addr);

      // Shortest address is ::1, this results in 3 parts...
      if (sizeof($hexquads) < 3) {
        throw new FormatException('Address contains less than 1 hexquad part: ['.$addr.']');
      }

      if ('' == $hexquads[0]) array_shift($hexquads);
      foreach ($hexquads as $hq) {
        if ('' == $hq) {
          $out.= str_repeat('0000', 8 - (sizeof($hexquads)- 1));
          continue;
        }

        // Catch cases like ::ffaadd00::
        if (strlen($hq) > 4) {
          throw new FormatException('Detected hexquad w/ more than 4 digits in ['.$addr.']');
        }

        $out.= str_repeat('0', 4- strlen($hq)).$hq;
      }
      
      return $out;
    }
        
    /**
     * Retrieve human-readable form;
     *
     * this method will shorten upon the first possible occasion, not on the
     * occasion where shortening will save the most space.
     *
     * @return  string
     */
    public function getAddress() {
      $skipZero= FALSE; $hasSkipped= FALSE; $hexquads= array();
      for ($i= 0; $i < 16; $i+=2) {
        if (!$hasSkipped && "\x00\x00" == $this->addr{$i}.$this->addr{$i+1}) {
          $skipZero= TRUE;
          continue;
        }
        if ($skipZero) {
          if (0 === sizeof($hexquads)) { $hexquads[]= ''; }
          $hexquads[]= '';
          $hasSkipped= TRUE;
          $skipZero= FALSE;
        }
        if ("\x00\x00" == $this->addr{$i}.$this->addr{$i+1}) {
          $hexquads[]= '0';
        } else {
          $hexquads[]= ltrim(this(unpack('H*', $this->addr{$i}.$this->addr{$i+1}), 1), '0');
        }
      }
      
      if ($skipZero) { $hexquads[]= ''; $hexquads[]= ''; }
      return implode(':', $hexquads);
    }    
    
    /**
     * Determine whether address is a loopback address
     *
     * @return  bool
     */
    public function isLoopback() {
      return "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x01" == $this->addr;
    }
    
    /**
     * Determine whether address is in the given subnet
     *
     * @param   string net
     * @return  bool
     * @throws  lang.FormatException in case net has invalid format
     */
    public function inSubnet(Network $net) {
      $addr= $net->getAddress();
      $mask= $net->getNetmask();
      
      $position= 0;
      while ($mask > 8) {
        if ($addr->addr{$position} != $this->addr{$position}) return FALSE;
        $position++;
        $mask-= 8;
      }

      if ($mask > 0) {
        return ord($addr->addr{$position}) >> (8- $mask) == ord($this->addr{$position}) >> (8- $mask);
      }
      
      return TRUE;
    }

    /**
     * Equals method
     *
     * @param   lang.Object cmp
     * @return  bool
     */
    public function equals($cmp) {
      return $cmp instanceof self && $cmp->addr === $this->addr;
    }

    /**
     * Get string representation
     *
     * @return  string
     */
    public function toString() {
      return 'ipv6:'.$this->getAddress();
    }

  }
?>
