<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.Date', 'webservices.soap.types.SoapType');
  
  /**
   * SOAP DateTime
   *
   * @see      xp://webservices.soap.types.SoapType
   * @see      http://www.w3.org/TR/xmlschema-2/#ISO8601 
   * @see      http://www.w3.org/TR/xmlschema-2/#dateTime
   * @purpose  DateTime type
   */
  class SOAPDateTime extends SoapType {
    public
      $value= NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed arg
     */
    public function __construct($arg) {
      $this->value= new Date($arg);
    }
    
    /**
     * Return a string representation for use in SOAP
     *
     * @access  public
     * @return  string ISO 8601 conform date (1977-12-14T11:55:0)
     */
    public function toString() {
      return $this->value->toString('Y-m-d\TH:i:s');
    }
    
    /**
     * Returns this type's name
     *
     * @access  public
     * @return  string
     */
    public function getType() {
      return 'xsd:dateTime';
    }
  }
?>
