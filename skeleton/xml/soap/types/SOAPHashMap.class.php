<?php
  uses('xml.soap.SOAPNode');
  
  class SOAPHashMap extends Object {
    var $_hash;
    
    function __construct($params) {
      $this->_hash= $params;
      $this->item= &new SOAPNode(array(
        'name'          => 'hash',
        'attribute'     => array(
          'xmlns:hash'  => 'http://xml.apache.org/xml-soap',
          'xsi:type'    => 'hash:Map'
        )
      ));
      foreach ($this->_hash as $key=> $value) {
        $item= &$this->item->addChild(new SOAPNode(array('name' => 'item')));
        $item->addChild(new SOAPNode(array(
          'name'        => 'key',
          'attribute'   => array(
            'xsi:type'  => 'xsd:string'
          ),
          'content'     => $key
        )));
        $item->addChild(new SOAPNode(array(
          'name'        => 'value',
          'attribute'   => array(
            'xsi:type'  => 'xsd:string'
          ),
          'content'     => $value
        )));
      }
      parent::__construct();
    }
    
    function toString() {
      return '';
    }
    
    function getType() {
      return 'hash:Map';
    }
    
    function getItemName() {
      return FALSE;
    }
  }
?>
