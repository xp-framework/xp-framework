<?php
  uses(
    'xml.Tree',
    'xml.Node'
  );
    
  class SOAPEnvelope extends Tree {
    var 
      $envelope,
      $body,
      $method,
      $module;
 
    function __construct($params= NULL) {
      Tree::__construct($params);
    }
    
    function create() {
      $this->root= new Node(array(
        'name'          => 'SOAP-ENV:Envelope',
        'attribute'     => array(
          'xmlns:SOAP-ENV'              => 'http://schemas.xmlsoap.org/soap/envelope/',
          'SOAP-ENV:encodingStyle'      => 'http://schemas.xmlsoap.org/soap/encoding/',
          'xmlns:SOAP-ENV'              => 'http://schemas.xmlsoap.org/soap/envelope/',
          'xmlns:xsi'                   => 'http://www.w3.org/1999/XMLSchema-instance',
          'xmlns:xsd'                   => 'http://www.w3.org/1999/XMLSchema'
        )
      ));
      $this->body= &$this->root->addChild(new Node(array(
        'name' => 'SOAP-ENV:Body'
      )));
      if (isset($this->method) && isset($this->module)) {
        $this->body->addChild(new Node(array(
          'name'          => 'ctl:'.$this->module,
          'attribute'     => array(
            'xmlns:ctl'   => $this->method
          )
        )));
      }
    }
    
    function &getFault() {
      if (!isset($this->body->children)) return NULL;
      
      foreach ($this->body->children as $idx=> $child) {
        if ($child->name== 'SOAP-ENV:Fault') return $this->body->children[$idx];
      }
      return NULL;
    }
    
    function addFault($faultCode, $faultString) {
      $fault= $this->body->addChild(new Node(array(
        'name'    => 'SOAP-ENV:Fault'
      )));
      $fault->addChild(new Node(array(
        'name'    => 'faultcode',
        'content' => $faultCode
      )));
      $fault->addChild(new Node(array(
        'name'    => 'faultstring',
        'content' => $faultString
      )));
    }

    function checkData() {
      if ('SOAP-ENV:Envelope' != $this->root->name) return throw(
        E_FORMAT_EXCEPTION, $this->root->name.' is not a the valid SOAP root element'
      );
      foreach ($this->envelope->children as $idx=> $child) {
        if ('SOAP-ENV:Body' == $child->name) {
          $this->body= &$this->root->children[$idx];
          break;
        }
      }
      return 0;
    }

    function fromString($string) {
      if (!parent::fromString($string)) return 0;
      if (!$this->checkData()) return 0;
      return 1;
    }
        
    function fromFile($fileName) {
      if (!parent::fromFile($fileName)) return 0;
      if (!$this->checkData()) return 0;
      return 1;
    }
  }
?>
