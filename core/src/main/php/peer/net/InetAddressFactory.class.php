<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.net.Inet4Address', 'peer.net.Inet6Address');

  /**
   * InetAddress Factory
   *
   * @test      xp://net.xp_framework.unittest.peer.net.InetAddressFactoryTest
   * @purpose   Factory class
   */
  class InetAddressFactory extends Object {

    /**
     * Parse address from string
     *
     * @param   string string
     * @return  peer.InetAddress
     * @throws  lang.FormatException if address could not be matched
     */
    public function parse($string) {
      if (4 == sscanf($string, '%d.%d.%d.%d', $a, $b, $c, $d))
        return new Inet4Address($string);

      if (preg_match('#^[a-f0-9\:]+$#', $string))
        return new Inet6Address($string);

      throw new FormatException('Given argument does not look like an IP address: '.$string);
    }

    /**
     * Parse address from string, return NULL on failure
     *
     * @param   string string
     * @return  peer.InetAddress
     */
    public function tryParse($string) {
      try {
        return $this->parse($string);
      } catch (FormatException $e) {
        return NULL;
      }
    }
  }
?>
