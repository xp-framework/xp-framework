<?php
  import('xml.Node');

  class WSDLNode extends Node {
  
    function setContent($content) {
      Node::setContent($content);
      $this->attribute['xsi:type']= 'xsd:'.gettype($content);
    }
    
    function &addChild($child) {
      $child->attribute['xsi:type']= 'xsd:struct';
      return Node::addChild($child);
    }
  }
?>
