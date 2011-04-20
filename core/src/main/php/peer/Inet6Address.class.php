<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.InetAddress');
  
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
     * Normalize address
     *
     * @param   string addr
     * @return  string
     */
    protected static function normalize($addr) {
      $out= '';
      $hexquads= explode(':', $addr);
      if ('' == $hexquads[0]) array_shift($hexquads);
      foreach ($hexquads as $hq) {
        if ('' == $hq) {
          $out.= str_repeat('0000', 8 - (sizeof($hexquads)- 1));
          continue;
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
    public function inSubnet($net) {
      list($addr, $mask)= explode('/', $net);
      $addr= new self($addr);
      
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
  }
?>
