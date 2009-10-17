<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  
  uses(
    'xml.Tree',
    'xml.Node',
    'webservices.xmlrpc.XmlRpcFault',
    'util.Date',
    'webservices.xmlrpc.XmlRpcEncoder',
    'webservices.xmlrpc.XmlRpcDecoder',
    'scriptlet.rpc.AbstractRpcMessage'
  );

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
   * @see      xp://webservices.xmlrpc.XmlRpcClient
   * @purpose  Represent message
   */
  class XmlRpcMessage extends Object implements AbstractRpcMessage {
    public
      $tree = NULL;

    public
      $class  = '',
      $method = '';

    /**
     * Create a message
     *
     */
    public function create($reqmsg= NULL) { }
    
    /**
     * Construct a XML-RPC message from a string
     *
     * <code>
     *   $msg= XmlRpcMessage::fromString('<methodCall>...</methodCall>');
     * </code>
     *
     * @param   string string
     * @return  webservices.xmlrpc.XmlRpcMessage
     */
    public static function fromString($string) { }
    
    /**
     * Set encoding
     *
     * @param   string encoding
     */
    public function setEncoding($encoding) { }
    
    /**
     * Retrieve encoding
     *
     * @return  string
     */
    public function getEncoding() {
      return 'iso-8859-1';
    }
    
    /**
     * Retrieve Content-type for requests.
     *
     * @return  string
     */
    public function getContentType() { return 'text/xml'; }
    
    /**
     * Set the data for the message.
     *
     * @param   mixed arr
     */
    public function setData($arr) {
      $encoder= new XmlRpcEncoder();

      $params= $this->tree->root->addChild(new Node('params'));
      if (sizeof($arr)) foreach (array_keys($arr) as $idx) {
        $n= $params->addChild(new Node('param'));
        $n->addChild($encoder->encode($arr[$idx]));
      }
    }
    
    /**
     * Retrieve string representation of message as used in the
     * protocol.
     *
     * @return  string
     */
    public function serializeData() {
      return $this->tree->getDeclaration()."\n".$this->tree->getSource(0);
    }
    
    /**
     * Return the data from the message.
     *
     * @return  mixed
     */
    public function getData() {
      $ret= array();
      foreach (array_keys($this->tree->root->children) as $idx) {
        if ('params' != $this->tree->root->children[$idx]->getName())
          continue;
        
        // Process params node
        $decoder= new XmlRpcDecoder();
        foreach (array_keys($this->tree->root->children[$idx]->children) as $params) {
          $ret[]= $decoder->decode($this->tree->root->children[$idx]->children[$params]->children[0]);
        }
        
        return $ret;
      }
      
      throw new IllegalStateException('No node "params" found.');
    }
    
    /**
     * Set a fault for this message. This overwrites any previously set
     * return values.
     *
     * @param   int faultcode
     * @param   string faultstring
     */
    public function setFault($faultcode, $faultstring) {
      $encoder= new XmlRpcEncoder();
      
      $this->tree->root->children[0]= new Node('fault');
      $this->tree->root->children[0]->addChild($encoder->encode(array(
        'faultCode'   => $faultcode,
        'faultString' => $faultstring
      )));
    }
    
    /**
     * Retrieve the fault if there is one.
     *
     * @return  webservices.xmlrpc.XmlRpcFault or NULL if no fault exists
     */
    public function getFault() {

      // First check whether the fault-node exists
      if (
        !is('xml.Node', $this->tree->root->children[0]) ||
        'fault' != $this->tree->root->children[0]->getName()
      ) {
        return NULL;
      }
      
      $decoder= new XmlRpcDecoder();
      $data= $decoder->decode($this->tree->root->children[0]->children[0]);
      $f= new XmlRpcFault($data['faultCode'], $data['faultString']);
      return $f;
    }
    
    /**
     * Set Class
     *
     * @param   string class
     */
    public function setHandlerClass($class) {
      $this->class= $class;
    }

    /**
     * Get Class
     *
     * @return  string
     */
    public function getHandlerClass() {
      return $this->class;
    }

    /**
     * Set Method
     *
     * @param   string method
     */
    public function setMethod($method) {
      $this->method= $method;
    }

    /**
     * Get Method
     *
     * @return  string
     */
    public function getMethod() {
      return $this->method;
    }
  } 
?>
