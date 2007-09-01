<?php
/* This class is part of the XP framework
 *
 * $Id: SOAPLong.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace webservices::soap::types;

  uses('webservices.soap.types.SoapType');
  
  /**
   * Represents a long value. This class can be used to circumvent
   * the problem that some strong typed languages cannot cast ints
   * into a long (as PHP does automagically).
   *
   */
  class SOAPLong extends SoapType {
    public
      $long;
      
    /**
     * Constructor
     *
     * @param   int long
     */  
    public function __construct($long) {
      $this->long= number_format($long, 0, FALSE, FALSE);
    }
    
    /**
     * Return a string representation for use in SOAP
     *
     * @return  string 
     */    
    public function toString() {
      return (string)$this->long;
    }
    
    /**
     * Returns this type's name
     *
     * @return  string
     */
    public function getType() {
      return 'xsd:long';
    }
  }
?>
