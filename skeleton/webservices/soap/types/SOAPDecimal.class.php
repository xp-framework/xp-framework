<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('webservices.soap.types.SoapType');
  
  /**
   * Represents a decimal value.
   *
   */
  class SOAPDecimal extends SoapType {
    var
      $decimal;
      
    /**
     * Constructor
     *
     * @param   int value
     * @param   int decimals
     */  
    function __construct($value, $decimal) {
      $this->decimal= number_format($value, $decimal, '.', FALSE);
    }
    
    /**
     * Return a string representation for use in SOAP
     *
     * @return  string 
     */    
    function toString() {
      return (string)$this->decimal;
    }
    
    /**
     * Returns this type's name
     *
     * @return  string
     */
    function getType() {
      return 'xsd:decimal';
    }
  }
?>
