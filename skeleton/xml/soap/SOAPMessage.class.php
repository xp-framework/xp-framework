<?php
  uses('xml.Tree');
  uses('xml.Node');
  uses('xml.soap.SOAPNode');
  uses('xml.soap.SOAPFault');
  
  class SOAPMessage extends Tree {
    var $body;
    var $namespace= 'ctl';
    var $encoding= XML_ENCODING_DEFAULT;
    
    var $nodeType= 'SOAPNode';

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */    
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
    
    /**
     * Setzt die Daten
     *
     * @param   array arr Array aller zu übergebender Daten
     */
    function setData($arr) {
      $node= new SOAPNode();
      $node->namespace= $this->namespace;
      $node->fromArray($arr, 'item');
      if (!empty($node->children)) foreach ($node->children as $i=> $child) {
        $this->root->children[0]->children[0]->addChild($child);
      }
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */    
    function _recurseData(&$node, $names= FALSE) {
      $results= array();
      if (!empty($node->children)) foreach ($node->children as $child) {
        $idx= $names ? $child->name : sizeof($results);
        
        if (
          isset($child->attribute['xsi:null']) or       // Java
          isset($child->attribute['xsi:nil'])           // SOAP::Lite
        ) {
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
   
          case 'SOAPStruct':
          case 'struct':      
          case 'ur-type':
            if ($regs[1]== 'xsd') {
              $results[$idx]= $this->_recurseData($child, TRUE);
              break;
            }
            $results[$idx]= new StdClass();
            $ret= $this->_recurseData($child, TRUE);
            foreach ($ret as $key=> $val) {
              $results[$idx]->$key= $val;
            }
            break;
            
          default:
            if (!empty($child->children)) {
              $results[$idx]= $this->_recurseData($child, TRUE);
              break;
            }
            $results[$idx]= $child->getContent($this->encoding);

        }
      }
      return $results;
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */    
    function setFault($faultcode, $faultstring, $faultactor= NULL, $detail= NULL) {
      $this->root->children[0]->children[0]= new SOAPNode();
      $this->root->children[0]->children[0]->fromObject(new SOAPFault(array(
        'faultcode'      => $faultcode,
        'faultstring'    => $faultstring,
        'faultactor'     => $faultactor,
        'detail'         => $detail
      )));
      unset($this->root->children[0]->children[0]->attribute);
      $this->root->children[0]->children[0]->name= 'SOAP-ENV:Fault';
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
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
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function getData() {
      foreach ($this->root->attribute as $key=> $val) { // Namespace suchen
        if ($val == $this->action) $this->namespace= substr($key, strlen('xmlns:'));
      }
      $return= $this->_recurseData($this->root->children[0]->children[0]);
      return $return;
    }
  }
?>
