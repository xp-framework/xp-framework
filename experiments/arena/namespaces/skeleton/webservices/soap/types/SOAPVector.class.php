<?php
/* This class is part of the XP framework
 *
 * $Id: SOAPVector.class.php 10191 2007-05-03 13:03:25Z olli $
 */

  namespace webservices::soap::types;

  uses('webservices.soap.xp.XPSoapNode', 'webservices.soap.types.SoapType');

  /**
   * Vector type as serialized and recogned by Apache SOAP.
   *
   * @see      xp://webservices.soap.types.SoapType
   * @purpose  Vector type
   */
  class SOAPVector extends SoapType {
    public 
      $_vector;
    
    /**
     * Constructor
     *
     * @param   array params
     */
    public function __construct($params) {
      $this->_vector= $params;
      $this->item= new webservices::soap::xp::XPSoapNode('vec', NULL, array(
        'xmlns:vec'   => 'http://xml.apache.org/xml-soap',
        'xsi:type'    => 'vec:Vector'
      ));
    }
    
    /**
     * Return a string representation for use in SOAP
     *
     * @return  mixed
     */
    public function toString() {
      return $this->_vector;
    }
    
    /**
     * Returns this type's name
     *
     * @return  string
     */
    public function getType() {
      return 'vec:Vector';
    }
  }
?>
