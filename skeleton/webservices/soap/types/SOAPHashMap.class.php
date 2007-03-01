<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('webservices.soap.SOAPNode', 'webservices.soap.types.SoapType');
  
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
      $this->item= new SOAPNode('hash', NULL, array(
        'xmlns:hash'  => 'http://xml.apache.org/xml-soap',
        'xsi:type'    => 'hash:Map'
      ));
      foreach ($params as $key => $value) {
        $this->item->addChild(SOAPNode::fromArray(array(
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
