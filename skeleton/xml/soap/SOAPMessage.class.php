<?php
  define('E_SOAP_FAULT_EXCEPTION', 0x2FFF);
  
  import('xml.Tree');
  import('xml.Node');
  import('xml.soap.WSDLNode');
  import('xml.soap.SOAPFault');
  
  class SOAPMessage extends Tree {
    var $body;
    var $namespace= 'ctl';
    var $encoding= XML_ENCODING_DEFAULT;
    
    var $nodeType= 'WSDLNode';
    
    function create($action, $method) {
      $this->action= $action;
      $this->method= $method;

      $this->root= new Node(array(
        'name'          => 'SOAP-ENV:Envelope',
        'attribute'     => array(
          'xmlns:SOAP-ENV'              => 'http://schemas.xmlsoap.org/soap/envelope/', 
          'xmlns:xsd'                   => 'http://www.w3.org/2001/XMLSchema', 
          'xmlns:xsi'                   => 'http://www.w3.org/2001/XMLSchema-instance', 
          'xmlns:SOAP-ENC'              => 'http://schemas.xmlsoap.org/soap/encoding/', 
          'xmlns:si'                    => 'http://soapinterop.org/xsd', 
          'SOAP-ENV:encodingStyle'      => 'http://schemas.xmlsoap.org/soap/encoding/',
          'xmlns:'.$this->namespace     => $this->action   
        )
      ));
      $this->root->addChild(new Node(array('name' => 'SOAP-ENV:Body')));
      $this->root->children[0]->addChild(new Node(array('name' => $this->namespace.':'.$this->method)));
    }
    
    function setData($arr) {
      $node= new WSDLNode(array(
        'namespace'     => $this->namespace
      ));
      $node->fromArray($arr, 'item');
      /*
      $node->attribute= array(
        'xsi:type'              => 'SOAP-ENC:Array',
        'SOAP-ENC:arrayType'    => 'xsd:ur-type['.sizeof($arr).']'
      );
      */
      $this->root->children[0]->children[0]->addChild($node->children[0]);
    }
    
    function _recurseData(&$node, $names= FALSE) {

      $results= array();
      foreach ($node->children as $child) {
        $idx= $names ? $child->name : sizeof($results);
        
        if (isset($child->attribute['xsi:nil'])) {
          $results[$idx]= NULL;
          continue;
        }
        
        // Typenabhängig
        if (!preg_match(
          '#^([^:]+):([^\[]+)(\[[0-9+]\])?$#', 
          $child->attribute['xsi:type'],
          $regs
        )) {
          // Zum Beispiel SOAP-ENV:Fault
          $regs= array(0, 'xsd', 'struct');
        }
        
        // echo "{$child->name} is {$regs[2]}\n";
        switch ($regs[2]) {
          case 'Array':
            $results[$idx]= $this->_recurseData($child);
            break;
          
          case 'ur-type':
            $results[$idx]= $this->_recurseData($child, TRUE);
            break;

          case 'SOAPStruct':
          case 'struct':
            $results[$idx]= new StdClass();
            $ret= $this->_recurseData($child, TRUE);
            foreach ($ret as $key=> $val) {
              $results[$idx]->$key= $val;
            }
            break;
            
          default:
            $results[$idx]= $child->getContent($this->encoding);

        }
      }
      return $results;
    }
    
    function setFault($faultcode, $faultstring, $faultactor= NULL) {
      $this->root->children[0]->children[0]= new WSDLNode();
      $this->root->children[0]->children[0]->fromObject(new SOAPFault(array(
        'faultcode'      => $faultcode,
        'faultstring'    => $faultstring,
        'faultactor'     => $faultactor
      )));
      unset($this->root->children[0]->children[0]->attribute);
      $this->root->children[0]->children[0]->name= 'SOAP-ENV:Fault';
    }

    function getFault() {
      if ($this->root->children[0]->children[0]->name == 'SOAP-ENV:Fault') {
        $fault= new SOAPFault();
        foreach ($this->root->children[0]->children[0]->children as $child) {
          $fault->{$child->name}= $child->getContent();
        }
        return $fault;
      }
      return FALSE;
    }
    
    function getData() {
          
      // Namespace suchen
      foreach ($this->root->attribute as $key=> $val) {
        if ($val == $this->action) $this->namespace= substr($key, strlen('xmlns:'));
      }
      
      // Rekursiv durchgehen
      $return= $this->_recurseData($this->root->children[0]->children[0]);
      
      // Fehler?
      if (empty($return)) {
        return throw(E_SOAP_FAULT_EXCEPTION, $return);
      }
      
      // Nur das nullte Element ist interessant
      return $return[0];
    }
  }
?>
