<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xml.soap.SOAPNode');
  
  class SOAPVector extends Object {
    var $_vector;
    
    function __construct($params) {
      $this->_vector= $params;
      $this->item= &new SOAPNode(array(
        'name'          => 'vec',
        'attribute'     => array(
          'xmlns:vec'   => 'http://xml.apache.org/xml-soap',
          'xsi:type'    => 'vec:Map'
        )
      ));
      parent::__construct();
    }
    
    function toString() {
      return $this->_vector;
    }
    
    function getType() {
      return 'vec:Vector';
    }
    
    function getItemName() {
      return FALSE;
    }

  }
?>  
