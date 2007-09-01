<?php
/* This class is part of the XP framework
 *
 * $Id: SOAPBase64Binary.class.php 10594 2007-06-11 10:04:54Z friebe $
 */

  namespace webservices::soap::types;

  uses('webservices.soap.types.SoapType');

  /**
   * SOAP Base64 binary
   *
   * @see      xp://webservices.soap.xp.XPSoapNode
   * @purpose  Transport base64 encoded data
   */
  class SOAPBase64Binary extends SoapType {
    public
      $string,
      $encoded;
    
    /**
     * Constructor
     *
     * @param   string string
     * @param   bool encoded default FALSE
     */
    public function __construct($string, $encoded= ) {
      if ($encoded) {
        $this->string= base64_decode($string);
        $this->encoded= $string;
      } else {
        $this->string= $string;
        $this->encoded= base64_encode($string);
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
      return 'xsd:base64Binary';
    }
    
    /**
     * Indicates whether the compared binary equals this one.
     *
     * @param   webservices.soap.types.SOAPBase64Binary cmp
     * @return  bool TRUE if both binaries are equal
     */
    public function equals($cmp) {
      return is('webservices.soap.types.SOAPBase64Binary', $cmp) && (0 === strcmp($this->string, $cmp->string));
    }    
  }
?>
