<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('webservices.soap.xp.XPSoapNode', 'webservices.soap.types.SoapType');

  /**
   * Vector type as serialized and recogned by Apache SOAP.
   *
   * @see      xp://webservices.soap.types.SoapType
   * @purpose  Vector type
   */
  class SOAPVector extends Object implements SoapType {
    public 
      $_vector;
    
    /**
     * Constructor
     *
     * @param   array params
     */
    public function __construct($params) {
      $this->_vector= $params;
      $this->item= new XPSoapNode('vec', NULL, array(
        'xmlns:vec'   => 'http://xml.apache.org/xml-soap',
        'xsi:type'    => 'vec:Vector'
      ));
    }
    
    /**
     * Return a string representation for use in SOAP
     *
     * @return  var
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

    /**
     * Retrieve item name
     *
     * @return  mixed
     */
    public function getItemName() {
      return FALSE;
    }

    /**
     * Retrieve type as native SOAP type
     *
     * @return  php.SoapVar
     */
    public function asSoapType() {
      return new SoapVar($this->_vector, SOAP_ENC_ARRAY);
    }
  }
?>
