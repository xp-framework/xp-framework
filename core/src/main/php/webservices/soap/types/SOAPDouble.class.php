<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('webservices.soap.types.SoapType');
  
  /**
   * Represents a double value.
   *
   */
  class SOAPDouble extends SoapType {
    public
      $double;
      
    /**
     * Constructor
     *
     * @param   double double
     */  
    public function __construct($double) {
      $this->double= number_format($double, 0, FALSE, FALSE);
    }
    
    /**
     * Return a string representation for use in SOAP
     *
     * @return  string 
     */    
    public function toString() {
      return (string)$this->double;
    }
    
    /**
     * Returns this type's name
     *
     * @return  string
     */
    public function getType() {
      return 'xsd:double';
    }
  }
?>
