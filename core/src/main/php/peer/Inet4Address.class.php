<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.InetAddress');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class Inet4Address extends InetAddress {
  
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    protected static function ip2long($ip) {
      $i= 0; $addr= 0;
      foreach (explode('.', $ip) as $byte) {
        $addr|= ($byte << (8 * (3 - $i++)));
      }
      return $addr;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function __construct($address) {
      $this->addr= self::ip2long($address);
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function getAddress() {
      return long2ip($this->addr);
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function isLoopback() {
      return $this->addr >> 8 == 0x7F0000;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function inSubnet($net) {
      list ($addr, $mask)= explode('/', $net);
      if (empty($mask) || empty($addr) || $mask < 0 || $mask > 32) throw new FormatException('Invalid subnet notation given: use "host/mask"');
      $addrn= self::ip2long($addr);
      
      return $this->addr >> (32 - $mask) == $addrn >> (32 - $mask);
    }
  }
?>
