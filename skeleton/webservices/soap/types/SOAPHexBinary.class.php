<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('webservices.soap.types.SoapType');

  /**
   * SOAP Hex binary
   *
   * @see      xp://webservices.soap.xp.XPSoapNode
   * @purpose  Transport hex encoded data
   */
  class SOAPHexBinary extends SoapType {
    public
      $string,
      $encoded;
    
    /**
     * Constructor
     *
     * @param   string string
     * @param   bool encoded default FALSE
     */
    public function __construct($string, $encoded= FALSE) {
      if ($encoded) {
        $this->string= pack('H*', $string);
        $this->encoded= $string;
      } else {
        $this->string= $string;
        $this->encoded= bin2hex($string);
      }
    }
    
    /**
     * Return a string representation for use in SOAP
     *
     * @return  string 
     */
    public function toString() {
      return $this->encoded;
    }
    
    /**
     * Returns this type's name
     *
     * @return  string
     */
    public function getType() {
      return 'xsd:hexBinary';
    }
    
    /**
     * Indicates whether the compared binary equals this one.
     *
     * @param   webservices.soap.types.SOAPHexBinary cmp
     * @return  bool TRUE if both binaries are equal
     */
    public function equals($cmp) {
      return is('webservices.soap.types.SOAPHexBinary', $cmp) && (0 === strcmp($this->string, $cmp->string));
    }    
  }
?>
