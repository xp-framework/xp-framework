<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xml.soap.SOAPNode', 'xml.soap.types.SoapType');
  
  /**
   * Hashmap type as serialized and recogned by Apache SOAP.
   *
   * @see      xp://xml.soap.types.SoapType
   * @purpose  HashMap type
   */
  class SOAPHashMap extends SoapType {

    /**
     * Constructor
     *
     * @access  public
     * @param   array params
     */
    function __construct($params) {
      $this->item= &new SOAPNode('hash', NULL, array(
        'xmlns:hash'  => 'http://xml.apache.org/xml-soap',
        'xsi:type'    => 'hash:Map'
      ));
      foreach ($params as $key => $value) {
        $item= &$this->item->addChild(new SOAPNode('item'));
        $item->addChild(new SOAPNode('key', htmlspecialchars($key), array(
          'xsi:type'  => 'xsd:string'
        )));
        $this->item->_recurseArray($item, array('value' => $value));
      }
      parent::__construct();
    }
    
    /**
     * Returns this type's name
     *
     * @access  public
     * @return  string
     */
    function getType() {
      return 'hash:Map';
    }
  }
?>
