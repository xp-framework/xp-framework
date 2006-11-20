<?php
/* This class is part of the XP framework
 *
 * $Id: XmlRpcClient.class.php 5378 2005-07-25 12:58:56Z jens $ 
 */

  uses('org.json.rpc.JsonMessage');

  /**
   * This is a Json-RPC client
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
      static $serial= 1000;
      if (!is('scriptlet.rpc.transport.GenericHttpTransport', $this->transport))
        return throw(new IllegalArgumentException('Transport must be a scriptlet.rpc.transport.GenericHttpTransport'));
    
      $this->transport->setMessageClass(XPClass::forName('org.json.rpc.JsonMessage'));
      $args= func_get_args();
      
      $this->message= &new JsonMessage();
      $this->message->createCall(array_shift($args), time().(++$serial));
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
