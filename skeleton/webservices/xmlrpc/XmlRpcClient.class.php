<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'webservices.xmlrpc.XmlRpcResponseMessage', 
    'webservices.xmlrpc.XmlRpcRequestMessage',
    'webservices.xmlrpc.transport.XmlRpcTransport'
  );

  /**
   * This is an XML-RPC client; XML-RPC is a remote procedure call
   * protocol that uses XML as the message format.
   *
   * It has the same origins as SOAP, but has been developed to cure
   * some of the problems SOAP has: it is not nearly as complex as SOAP
   * and doesn't have all those (mostly unneccessary) features of SOAP.
   * The spec is short and precise, unlike SOAP's - thus, the various
   * implementations really understand themselves.
   *
   * <code>
   *   $c= new XmlRpcClient(new XmlRpcHttpTransport('http://xmlrpc.xp-framework.net'));
   *   
   *   try {
   *     $res= $c->invoke('sumAndDifference', 5, 3);
   *   } catch (XmlRpcFaultException $e) {
   *     $e->printStackTrace();
   *     exit(-1);
   *   }
   *
   *   echo $res;
   * </code>
   *
   * @test     xp://net.xp_framework.unittest.scriptlet.rpc.XmlRpcClientTest
   * @ext      xml
   * @see      http://xmlrpc.com
   * @purpose  Generic XML-RPC Client base class
   */
  class XmlRpcClient extends Object {
    public
      $transport  = NULL,
      $message    = NULL,
      $answer     = NULL;

    /**
     * Constructor.
     *
     * @param   webservices.xmlrpc.transport.XmlRpcTransport transport
     */
    public function __construct(XmlRpcHttpTransport $transport) {
      $this->transport= $transport;
    }
    
    /**
     * Set trace for debugging
     *
     * @param   util.log.LogCategory cat
     */
    public function setTrace($cat) {
      $this->transport->setTrace($cat);
    }
    
    /**
     * Invoke a method on a XML-RPC server
     *
     * @param   string method
     * @param   mixed vars
     * @return  mixed answer
     * @throws  lang.IllegalArgumentException
     * @throws  webservices.xmlrpc.XmlRpcFaultException
     */
    public function invoke() {
      $args= func_get_args();
      
      $this->message= new XmlRpcRequestMessage();
      $this->message->create(array_shift($args));
      $this->message->setData($args);
      
      // Send
      if (FALSE == ($response= $this->transport->send($this->message))) return FALSE;
      
      // Retrieve response
      if (FALSE == ($this->answer= $this->transport->retrieve($response))) return FALSE;
      
      $data= $this->answer->getData();
      return $data;
    }
  }
?>
