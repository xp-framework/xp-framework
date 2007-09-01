<?php
/* This class is part of the XP framework
 *
 * $Id: JsonClient.class.php 8651 2006-11-25 17:18:51Z kiesel $ 
 */

  namespace webservices::json::rpc;

  uses('webservices.json.rpc.JsonMessage');

  /**
   * This is a Json-RPC client
   *
   * @see       http://json-rpc.org/wiki/specification
   * @see       http://json.org/
   * @purpose   Json RPC Client base class
   */
  class JsonClient extends lang::Object {
    public
      $transport  = NULL,
      $message    = NULL,
      $answer     = NULL;

    /**
     * Constructor.
     *
     * @param   scriptlet.rpc.transport.GenericHttpTransport transport
     */
    public function __construct($transport) {
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
      static $serial= 1000;
      if (!is('scriptlet.rpc.transport.GenericHttpTransport', $this->transport))
        throw(new lang::IllegalArgumentException('Transport must be a scriptlet.rpc.transport.GenericHttpTransport'));
    
      $this->transport->setMessageClass(lang::XPClass::forName('webservices.json.rpc.JsonMessage'));
      $args= func_get_args();
      
      $this->message= new JsonMessage();
      $this->message->createCall(array_shift($args), time().(++$serial));
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
