<?php
/* This class is part of the XP framework
 *
 * $Id: SOAPHashMap.class.php 10015 2007-04-16 16:36:48Z kiesel $
 */

  namespace webservices::soap::types;

  uses('webservices.soap.xp.XPSoapNode', 'webservices.soap.types.SoapType');
  
  /**
   * Hashmap type as serialized and recogned by Apache SOAP.
   *
   * @see      xp://webservices.soap.types.SoapType
   * @purpose  HashMap type
   */
  class SOAPHashMap extends SoapType {

    /**
     * Constructor
     *
     * @param   array params
     */
    public function __construct($params) {
      $this->item= new webservices::soap::xp::XPSoapNode('hash', NULL, array(
        'xmlns:hash'  => 'http://xml.apache.org/xml-soap',
        'xsi:type'    => 'hash:Map'
      ));
      foreach ($params as $key => $value) {
        $this->item->addChild(webservices::soap::xp::XPSoapNode::fromArray(array(
          'key'   => $key,
          'value' => $value
        ), 'item'));
      }
    }
    
    /**
     * Return a string representation for use in SOAP
     *
     * @return  mixed
     */
    public function toString() {
      return '';
    }
    
    /**
     * Returns this type's name
     *
     * @return  string
     */
    public function getType() {
      return 'hash:Map';
    }
  }
?>
