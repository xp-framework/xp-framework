<?php
/* This class is part of the XP framework
 *
 * $Id: XmlRpcClient.class.php 5378 2005-07-25 12:58:56Z jens $ 
 */

  uses('org.json.rpc.JsonMessage');

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
   * @see       http://json-rpc.org/wiki/specification
   * @see       http://json.org/
   * @purpose   Json RPC Client base class
   */
  class JsonClient extends Object {
    var
      $transport  = NULL,
      $message    = NULL,
      $answer     = NULL;

    /**
     * Constructor.
     *
     * @access  public
     * @param   &scriptlet.rpc.transport.GenericHttpTransport transport
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
      if (!is('scriptlet.rpc.transport.GenericHttpTransport', $this->transport))
        return throw(new IllegalArgumentException('Transport must be a scriptlet.rpc.transport.GenericHttpTransport'));
    
      $this->transport->setMessageClass(XPClass::forName('org.json.rpc.JsonMessage'));
      $args= func_get_args();
      
      $this->message= &new JsonMessage();
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
