<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xml.xmlrpc.XmlRpcMessage');

  /**
   * This is a XML-RPC client; XML-RPC is a remote procedure call
   * protocol that uses XML as the message format.
   *
   * It has the same origins like SOAP, but has been developed to cure
   * some of the problems, SOAP has: it not nearly as complex as SOAP is
   * and does not have all those (mostly unneccessary) features SOAP does.
   * The spec is short and precise, unlike SOAP's - thus, the various
   * implementations really understand themselves.
   *
   * <code>
   *   uses('xml.xmlrpc.XmlRpcClient', 'xml.xmlrpc.transport.XmlRpcHttpTransport');
   *   $c= &new XmlRpcClient(new XMLRPCHTTPTransport('http://xmlrpc.xp-framework.net'));
   *   
   *   try(); {
   *     $res= $c->invoke('sumAndDifference', 5, 3);
   *   } if (catch('XmlRpcFaultException', $e)) {
   *     $e->printStackTrace();
   *     exit(-1);
   *   }
   *
   *   echo $res;
   * </code>
   *
   * @ext      xml
   * @see      http://xmlrpc.com
   * @purpose  Generic XML-RPC Client base class
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
      
      $this->message= &new XmlRpcRequestMessage();
      $this->message->create(array_shift($args));
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
