<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'xml.Tree',
    'xml.Node',
    'xml.wsdl.WsdlMessage',
    'xml.schema.XmlSchema'
  );

  /**
   * WSDL
   *
   * <code>
   *   uses('xml.wsdl.WsdlDocument');
   *   
   *   $d= new WsdlDocument('urn:GoogleSearch', 'urn:GoogleSearch');
   *   $d->addNamespace('xmlns:typens', 'urn:GoogleSearch');
   *   
   *   $d->addMessage(new WsdlMessage('doGoogleSearch', array(
   *     'key'            => 'string',
   *     'q'              => 'string',
   *     'start'          => 'int',
   *     'maxResults'     => 'int',
   *     'filter'         => 'boolean',
   *     'restrict'       => 'string',
   *     'safeSearch'     => 'boolean',
   *     'lr'             => 'string',
   *     'ie'             => 'string',
   *     'oe'             => 'string',
   *   )));
   *   $d->addMessage(new WsdlMessage('doGoogleSearchResponse', array(
   *     'return'        => array('GoogleSearchResult', 'typens')
   *   )));
   * 
   *   $s= new XmlSchema('urn:GoogleSearch');
   *   $s->addComplexType(new XmlSchemaStructure(
   *     'GoogleSearchResult', 
   *     WSDL_TYPE_COMPLEX,
   *     array(
   *       'documentFiltering'           => 'boolean',
   *       'searchComments'              => 'string',
   *       'estimatedTotalResultsCount'  => 'int',
   *       'estimateIsExact'             => 'boolean',
   *       'resultElements'              => array('ResultElementArray', 'typens'),
   *       'searchQuery'                 => 'string',
   *       'startIndex'                  => 'int',
   *       'endIndex'                    => 'int',
   *       'searchTips'                  => 'string',
   *       'directoryCategories'         => array('DirectoryCategoryArray', 'typens'),
   *       'searchTime'                  => 'double'
   *     )
   *   ));
   *   $d->setTypes($s);
   *
   *   echo $d->getSource(0);
   * </code>
   *
   * @purpose  WSDL
   * @experimental
   */
  class WsdlDocument extends Tree {
    const
      XMLNS_WSDL = 'http://schemas.xmlsoap.org/wsdl/',
      XMLNS_XSD = 'http://www.w3.org/2001/XMLSchema',
      XMLNS_SOAP = 'http://schemas.xmlsoap.org/wsdl/soap/',
      XMLNS_SOAPENC = 'http://schemas.xmlsoap.org/soap/encoding/';

    public 
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
    public function __construct($name= NULL, $targetNamespace= NULL) {
      parent::__construct();
      $this->root= new Node('definitions', NULL, array(
        'xmlns:xsd'       => XMLNS_XSD,
        'xmlns:soap'      => XMLNS_SOAP,
        'xmlns:soapenc'   => XMLNS_SOAPENC,
        'xmlns:wsdl'      => XMLNS_WSDL,
        'xmlns'           => XMLNS_WSDL
      ));
      if (NULL !== $name) self::setName($name);
      if (NULL !== $targetNamespace) self::setTargetNamespace($targetNamespace);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function addNamespace($name, $urn) {
      $this->root->attribute['xmlns:'.$name]= $urn;
    }
    
    /**
     * Set types
     *
     * @access  public
     * @param   &xml.schema.XmlSchema schema
     */
    public function setTypes(&$schema) {
      if (!is_a($schema, 'XmlSchema')) {
        trigger_error('Type: '.get_class($schema), E_USER_NOTICE);
        throw (new IllegalArgumentException('schema is not a xml.schema.XmlSchema'));
      }

      $this->types= $schema->getComplexTypes();
      
      // Build DOM
      $s= $this->root->addChild(new Node('xsd:schema', NULL, array(
        'xmlns'           => XMLNS_SCHEMA,
        'targetNamespace' => $schema->getTargetNamespace()
      )));
                
      foreach (array_keys($this->types) as $key) {
        $n= $s->addChild(new Node('xsd:complexType', NULL, array(
          'name'  => $this->types[$key]->getName()
        )));
        
        foreach ($this->types[$key]->getElements() as $element) {
          $n->addChild(new Node('xsd:element', NULL, array(
            'name'  => $element->name,
            'type'  => $element->namespace.':'.$element->type
          )));
        }
      }
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function addService() {
    
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function addPortType() {
    
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function addBinding() {
    
    }
    
    /**
     * Add a message
     *
     * @access  public
     * @param   &xml.soap.wsdl.WsdlMessage message
     * @return  &xml.soap.wsdl.WsdlMessage the added message
     * @throws  IllegalArgumentException when message is not a WsdlMessage object or message has already been added
     */
    public function addMessage(&$message) {
      if (!is_a($message, 'WsdlMessage')) {
        trigger_error('Type: '.get_class($message), E_USER_NOTICE);
        throw (new IllegalArgumentException('message is not a xml.wsdl.WsdlMessage'));
      }
      
      // Does this message already exists
      if (isset($this->messages[$message->name])) {
        throw (new IllegalArgumentException('Cannot add message "'.$message->name.'" twice'));
      }
      
      // Put this in a associative array so searching is O(1)
      $this->messages[$message->name]= array();
      $this->messages[$message->name]['obj']= $message;
      $this->messages[$message->name]['node']= $this->root->addChild(new Node(
        'message',
        NULL,
        array('name' => $message->name)
      ));
      
      // Build DOM
      foreach (array_keys($message->parts) as $key) {
        $n= $this->messages[$message->name]['node']->addChild(new Node(
          'part',
          NULL,
          array('name' => $key)
        ));
        
        // Type
        if (NULL != $message->parts[$key]->type) {
          $n->attribute['type']= $message->parts[$key]->namespace.':'.$message->parts[$key]->type;
        }
        
        // Element
        if (NULL != $message->parts[$key]->element) {
          $n->attribute['element']= $message->parts[$key]->element;
        }
      }
      
      return $message;
    }
    
    /**
     * Retrieve a message by name
     *
     * @access  public
     * @param   string name
     * @return  &xml.wsdl.WsdlMessage message or NULL if none is found
     */
    public function getMessageByName($name) {
      if (isset($this->messages[$name])) return $this->messages[$name]['obj']; else return NULL;
    }
    
    /**
     * Get first message
     *
     * @access  public
     * @return  &xml.wsdl.WsdlMessage message
     */
    public function getFirstMessage() {
      reset($this->messages);
      return $this->messages[key($this->messages)]['obj'];
    }
    
    /**
     * Get next message
     *
     * <code>
     *   $msg= $wsdl->getFirstMessage();
     *   do {
     *     var_dump($msg);
     *   } while ($wsdl->getNextMessage());
     * </code>
     *
     * @access  public
     * @return  &xml.wsdl.WsdlMessage message
     */
    public function getNextMessage() {
      if (FALSE === next($this->messages)) return FALSE;
      return $this->messages[key($this->messages)]['obj'];
    }
    
    /**
     * Set this WSDL's name
     *
     * @access  publiuc
     * @param   string name
     */
    public function setName($name) {
      $this->root->attribute['name']= $name;
    }
    
    /**
     * Get this WSDL's name
     *
     * @access  public
     * @return  string name
     */
    public function getName() {
      return (isset($this->root->attribute['name']) 
        ? $this->root->attribute['name']
        : NULL
      );
    }
    
    /**
     * Set this WSDL's target namespace
     *
     * @access  public
     * @param   string ns
     */
    public function setTargetNamespace($ns) {
      $this->root->attribute['targetNamespace']= $ns;
    }

    /**
     * Get this WSDL's target namespace
     *
     * @access  public
     * @return  string
     */
    public function getTargetNamespace() {
      return (isset($this->root->attribute['targetNamespace']) 
        ? $this->root->attribute['targetNamespace']
        : NULL
      );
    }

  }
?>
