<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'peer.net.Network',
    'peer.net.InetAddressFactory'
  );

  /**
   * Description of NetworkParser
   *
   * @purpose   Parse network addresses
   */
  class NetworkParser extends Object {
    protected
      $addressParser  = NULL;

    /**
     * Constructor
     *
     */
    public function __construct() {
      $this->addressParser= new InetAddressFactory();
    }

    /**
     * Parse given string into network object
     *
     * @param   string string
     * @return  peer.Network
     * @throws  lang.FormatException if string could not be parsed
     */
    public function parse($string) {
      if (2 !== sscanf($string, '%[^/]/%d$', $addr, $mask)) 
        throw new FormatException('Given string cannot be parsed to network: ['.$string.']');

      return new Network($this->addressParser->parse($addr), $mask);
    }
  }
?>
