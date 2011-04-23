<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'peer.net.InetAddressFactory',
    'peer.net.Inet4Address'
  );

  class NameserverLookup extends Object {

    /**
     * Perform "real" dns lookup
     *
     * @param   string what
     * @param   Int type
     * @return  array
     */
    protected function _nativeLookup($what, $type) {
      return dns_get_record($what, $type);
    }

    /**
     * Lookup all inet4 addresses
     *
     * @param   string host
     * @return  peer.netInet4Address[]
     */
    public function lookupAllInet4($host) {
      $res= array();
      foreach ($this->_nativeLookup($host, DNS_A) as $addr) {
        $res[]= new Inet4Address($addr['ip']);
      }

      return $res;
    }

    /**
     * Lookup inet4 addresses
     *
     * @param   string host
     * @return  peer.netInet4Address[]
     */
    public function lookupInet4($host) {
      $addr= $this->_nativeLookup($host, DNS_A);
      if (sizeof($addr) < 1) throw new ElementNotFoundException('No record found for "'.$host.'"');

      return new Inet4Address($addr[0]['ip']);
    }

    /**
     * Lookup all inet6 addresses
     *
     * @param   string host
     * @return  peer.net.Inet6Address
     */
    public function lookupAllInet6($host) {
      $res= array();
      foreach ($this->_nativeLookup($host, DNS_AAAA) as $addr) {
        $res[]= new Inet6Address($addr['ip']);
      }

      return $res;
    }

    /**
     * Lookup inet4 addresses
     *
     * @param   string host
     * @return  peer.netInet4Address[]
     */
    public function lookupInet6($host) {
      $addr= $this->_nativeLookup($host, DNS_AAAA);
      if (sizeof($addr) < 1) throw new ElementNotFoundException('No record found for "'.$host.'"');

      return new Inet6Address($addr[0]['ip']);
    }

    /**
     * Lookup all addresses
     *
     * @param   string host
     * @return  peer.net.InetAddress[]
     */
    public function lookupAll($host) {
      $res= array(); $parser= new InetAddressFactory();
      foreach ($this->_nativeLookup($host, DNS_A|DNS_AAAA) as $addr) {
        $res[]= $parser->parse($addr['ip']);
      }

      return $res;
    }

    /**
     * Lookup inet4 addresses
     *
     * @param   string host
     * @return  peer.netInet4Address[]
     */
    public function lookup($host) {
      $addr= $this->_nativeLookup($host, DNS_A|DNS_AAAA);
      if (sizeof($addr) < 1) throw new ElementNotFoundException('No record found for "'.$host.'"');

      $parser= new InetAddressFactory();
      return $parser->parse($addr[0]['ip']);
    }
  }


?>
