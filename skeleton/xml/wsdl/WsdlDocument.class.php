<?php
  uses(
    'xml.Tree', 
    'xml.Node',
    'xml.wsdl.WsdlMessage'
  );
  
  define('XMLNS_WSDL',    'http://schemas.xmlsoap.org/wsdl/');
  define('XMLNS_XSD',     'http://www.w3.org/2001/XMLSchema');
  define('XMLNS_SOAP',    'http://schemas.xmlsoap.org/wsdl/soap/');
  define('XMLNS_SOAPENC', 'http://schemas.xmlsoap.org/soap/encoding/');
  
  /**
   *
   */
  class WsdlDocument extends Tree {
    var 
      $types    = array(),
      $messages = array(),
      $portTypes= array(),
      $bindings = array(),
      $service  = array();
  
    /**
     * Constructor
     *
     * @access  public
     */
    function __construct($name= NULL, $targetNamespace= NULL) {
      parent::__construct();
      $this->root= &new Node(array(
        'name'          => 'definitions',
        'attribute'     => array(
          'xmlns:xsd'       => XMLNS_XSD,
          'xmlns:soap'      => XMLNS_SOAP,
          'xmlns:soapenc'   => XMLNS_SOAPENC,
          'xmlns:wsdl'      => XMLNS_WSDL,
          'xmlns'           => XMLNS_WSDL
        )
      ));
      if (NULL !== $name) $this->setName($name);
      if (NULL !== $targetNamespace) $this->setTargetNamespace($targetNamespace);
    }
    
    function addNamespace($name, $urn) {
      $this->root->attribute['xmlns:'.$name]= $urn;
    }
    
    function setTypes(&$schema) {
      
    }
    
    function addService() {
    
    }
    
    function addPortType() {
    
    }
    
    function addBinding() {
    
    }
    
    /**
     * Add a message
     *
     * @access  public
     * @param   &xml.soap.wsdl.WsdlMessage message
     * @throws  IllegalArgumentException when message is not a WsdlMessage object
     *          or message has already been added
     * @return  &xml.soap.wsdl.WsdlMessage message
     */
    function addMessage(&$message) {
      if (!is_a($message, 'WsdlMessage')) {
        return throw(new IllegalArgumentException('message is not a WsdlMessage'));
      }
      if (isset($this->messages[$message->name])) {
        return throw(new IllegalArgumentException('Cannot add message "'.$message->name.'" twice'));
      }
      
      $this->messages[$message->name]= array();
      $this->messages[$message->name]['obj']= &$message;
      $this->messages[$message->name]['node']= &$this->root->addChild(new Node(array(
        'name'        => 'message',
        'attribute'   => array('name' => $message->name)
      )));
      foreach (array_keys($message->parts) as $key) {
        $n= &$this->messages[$message->name]['node']->addChild(new Node(array(
          'name'        => 'part',
          'attribute'   => array('name' => $key)
        )));
        if (NULL != $message->parts[$key]->type) {
          $n->attribute['type']= $message->parts[$key]->namespace.':'.$message->parts[$key]->type;
        }
        if (NULL != $message->parts[$key]->element) {
          $n->attribute['element']= $message->parts[$key]->element;
        }
      }
      
      return $message;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function &getMessageByName($name) {
      return isset($this->messages[$name]) ? $this->messages[$name]['obj'] : NULL;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function getFirstMessage() {
      reset($this->messages);
      return key($this->messages);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function getNextMessage() {
      if (FALSE === next($this->messages)) return FALSE;
      return key($this->messages);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setName($name) {
      $this->root->attribute['name']= $name;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function getName() {
      return (isset($this->root->attribute['name']) 
        ? $this->root->attribute['name']
        : NULL
      );
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setTargetNamespace($ns) {
      $this->root->attribute['targetNamespace']= $ns;
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function getTargetNamespace() {
      return (isset($this->root->attribute['targetNamespace']) 
        ? $this->root->attribute['targetNamespace']
        : NULL
      );
    }

  }

?>
