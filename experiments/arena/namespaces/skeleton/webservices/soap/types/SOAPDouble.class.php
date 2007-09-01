<?php
/* This class is part of the XP framework
 *
 * $Id: SOAPDouble.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace webservices::soap::types;

  uses('webservices.soap.types.SoapType');
  
  /**
   * Represents a double value.
   *
   */
  class SOAPDouble extends SoapType {
    var
      $double;
      
    /**
     * Constructor
     *
     * @param   int double
     */  
    function __construct($double) {
      $this->double= number_format($double, 0, FALSE, FALSE);
    }
    
    /**
     * Return a string representation for use in SOAP
     *
     * @return  string 
     */    
    function toString() {
      return (string)$this->double;
    }
    
    /**
     * Returns this type's name
     *
     * @return  string
     */
    function getType() {
      return 'xsd:double';
    }
  }
?>
