<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xml.xmlrpc.XmlRpcMessage');

  /**
   * XML-RPC Client.
   *
   *  <code>
   *  uses('xml.xmlrpc.XmlRpcClient');
   *  $c= &new XmlRpcClient(new XMLRPCHTTPTransport('http://xmlrpc.xp-framework.net'));
   *  
   *  try(); {
   *    $res= $c->invoke('sumAndDifference', 5, 3);
   *  } if (catch('XmlRpcFaultException', $e)) {
   *    $e->printStackTrace();
   *    exit(-1);
   *  }
   *
   *  echo $res;
   *
   * @ext      xml
   * @see      http://xmlrpc.com
   * @purpose  XML-RPC Client
   */
  class XmlRpcClient extends Object {
    var
      $transport  = NULL,
      $message    = NULL,
      $answer     = NULL;

    /**
     * Constructor.
     *
     * @access  public
     * @param   &xml.xmlrpc.transport.XmlRpcTransport transport
     */
    function __construct(&$transport) {
      $this->transport= &$transport;
    }
    
    /**
     * Set trace for debugging
     *
     * @access  public
     * @param   &util.log.LogCategory cat
     */
    function setTrace(&$cat) {
      $this->transport->setTrace($cat);
    }
    
    /**
     * Invoke a method on a XML-RPC server
     *
     * @access  public
     * @param   string method
     * @param   mixed vars
     * @return  mixed answer
     * @throws  lang.IllegalArgumentException
     * @throws  xml.xmlrpc.XmlRpcFaultException
     */
    function invoke() {
      if (!is('xml.xmlrpc.transport.XmlRpcTransport', $this->transport))
        return throw(new IllegalArgumentException('Transport must be a xml.xmlrpc.transport.XmlRpcTransport'));
    
      $args= func_get_args();
      
      $this->message= &new XmlRpcMessage();
      $this->message->create(XMLRPC_METHODCALL, array_shift($args));
      $this->message->setData($args);
      
      // Send
      if (FALSE == ($response= &$this->transport->send($this->message))) return FALSE;
      
      // Retrieve response
      if (FALSE == ($this->answer= &$this->transport->retrieve($response))) return FALSE;
      
      $data= $this->answer->getData();
      return $data;
    }
  }
?>
