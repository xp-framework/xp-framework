<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  
  uses('xml.Tree', 'xml.Node', 'xml.xmlrpc.XmlRpcFault');

  // Message-types
  define('XMLRPC_METHODCALL', 'methodCall');
  define('XMLRPC_RESPONSE',   'methodResponse');

  /**
   * This class represents the message that is exchanged
   * in the communication between the client and server.
   *
   * In the communication in XML-RPC, a message is being sent
   * from the client to the server which is a XML document with
   * a root element <methodCall>. The first child is the <methodName>
   * tag which contains the name of the method to call.
   *
   * The server then returns a XML document with <methodResponse> as the
   * root element. It can have exactly one child: <params> containing the
   * called function's return value or <fault> in case some error has
   * occurred.
   *
   * @ext      xml
   * @see      xp://xml.xmlrpc.XmlRpcClient
   * @purpose  Represent message
   */
  class XmlRpcMessage extends Tree {
    var
      $type   = '',
      $method = '';

    /**
     * Create a XmlRpcMessage object
     *
     * @access  public
     * @param   string type
     * @param   string methodName default NULL
     */
    function create($type, $method= NULL) {
      $this->type= $type;
      $this->method= $method;
      
      $this->root= &new Node($this->type);
      if ($this->method)
        $this->root->addChild(new Node('methodName', $this->method));
    }
    
    /**
     * Construct a XML-RPC message from a string
     *
     * <code>
     *   $msg= &XmlRpcMessage::fromString('<methodCall>...</methodCall>');
     * </code>
     *
     * @model   static
     * @access  public
     * @param   string string
     * @return  &xml.xmlrpc.XmlRpcMessage
     */
    function &fromString($string) {
      return parent::fromString($string, 'XmlRpcMessage');
    }
    
    /**
     * Set the data for the message.
     *
     * @access  public
     * @param   &mixed arr
     */
    function setData(&$arr) {
      $params= &$this->root->addChild(new Node('params'));
      if (sizeof($arr)) foreach (array_keys($arr) as $idx) {
        $this->_marshall($params->addChild(new Node('param')), $arr[$idx]);
      }
    }
    
    /**
     * Recursivly serialize data to the given node
     *
     * @access  protected
     * @param   &xml.Node node
     * @param   &mixed data
     */
    function &_marshall(&$node, &$data) {
      $value= &$node->addChild(new Node('value'));
      
      switch (xp::typeOf($data)) {
        case 'integer':
          return $value->addChild(new Node('int', $data));
          break;
          
        case 'boolean':
          return $value->addChild(new Node('boolean', $data));
          break;
          
        case 'double':
        case 'float':
          return $value->addChild(new Node('double', $data));
          break;
        
        case 'array':
          $struct= &$value->addChild(new Node('struct'));
          if (sizeof($data)) foreach (array_keys($data) as $idx) {
            $member= &$struct->addChild(new Node('member'));
            $member->addChild(new Node('name', $idx));
            $this->_marshall($member, $data[$idx]);
          }
          return $struct;
          break;
        
        case 'string':
        default:
          return $value->addChild(new Node('string', $data));
          break;
      }
    }
    
    /**
     * Return the data from the message.
     *
     * @access  public
     * @return  &mixed
     */
    function &getData() {
      
      $ret= array();
      foreach (array_keys($this->root->children) as $idx) {
        if ('params' != $this->root->children[$idx]->getName())
          continue;
        
        // Process params node
        foreach (array_keys($this->root->children[$idx]->children) as $params) {
          $ret[]= &$this->_unmarshall($this->root->children[$idx]->children[$params]->children[0]);
        }
        
        return $ret;
      }
      
      return throw(new IllegalStateException('No node "params" found.'));
    }
    
    /**
     * Recursively deserialize data for the given node.
     *
     * @access  protected
     * @param   &xml.Node node
     * @return  &mixed
     * @throws  lang.IllegalArgumentException if the data cannot be deserialized
     */
    function &_unmarshall(&$node) {
      if (!is('xml.Node', $node->children[0]))
        return throw(new XMLFormatException('Tried to access nonexistant node.'));
        
      switch ($node->children[0]->getName()) {
        case 'struct':
          $ret= array();
          foreach (array_keys($node->children[0]->children) as $idx) {
            $data= array();
            $data[$node->children[0]->children[$idx]->children[0]->getName()]= &$node->children[0]->children[$idx]->children[0];
            $data[$node->children[0]->children[$idx]->children[1]->getName()]= &$node->children[0]->children[$idx]->children[1];
            $ret[$data['name']->getContent()]= &$this->_unmarshall($data['value']);
            unset($data);
          }
          return $ret;
          break;
          
        case 'array':
          $ret= array();
          foreach (array_keys($node->children[0]->children[0]->children) as $idx) {
            $ret[]= &$this->_unmarshall($node->children[0]->children[0]->children[$idx]);
          }
          return $ret;
          break;
        
        case 'int':
        case 'i4':
          return (int)$node->children[0]->getContent();
          break;
        
        case 'double':
          return (double)$node->children[0]->getContent();
          break;
        
        case 'boolean':
          return (bool)$node->children[0]->getContent();
          break;
        
        case 'string':
          return (string)$node->children[0]->getContent();
          break;
          
        default:
          return throw(new IllegalArgumentException('Could not decode node as it\'s type is not supported: '.$node->children[0]->getName()));
          break;
      }
    }
    
    /**
     * Set a fault for this message. This overwrites any previously set
     * return values.
     *
     * @access  public
     * @param   &xml.xmlrpc.XmlRpcFault fault
     */
    function setFault(&$fault) {
      $this->root->children[0]= &new Node('fault');
      
      $this->_marshall($this->root->children[0], $f= array(
        'faultCode'   => $fault->getFaultCode(),
        'faultString' => $fault->getFaultString()
      ));
    }
    
    /**
     * Retrieve the fault if there is one.
     *
     * @access  public
     * @return  &xml.xmlrpc.XmlRpcFault or NULL if no fault exists
     */
    function &getFault() {

      // First check whether the fault-node exists
      if (
        !is('xml.Node', $this->root->children[0]) ||
        'fault' != $this->root->children[0]->getName()
      ) {
        return NULL;
      }
      
      $data= &$this->_unmarshall($this->root->children[0]->children[0]);
      return new XmlRpcFault($data['faultCode'], $data['faultString']);
    }
  }
?>
