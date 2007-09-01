<?php
/* This class is part of the XP framework
 *
 * $Id: XmlRpcTransport.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace webservices::xmlrpc::transport;

  uses(
    'util.log.Traceable',
    'webservices.xmlrpc.XmlRpcMessage',
    'webservices.xmlrpc.XmlRpcFaultException'
  );

  /**
   * Base class for XML-RPC transports.
   *
   * @purpose  Base class.
   */
  class XmlRpcTransport extends lang::Object implements util::log::Traceable {
    public
      $cat  = NULL;
      
    /**
     * Set trace for debugging
     *
     * @param   util.log.LogCategory cat
     */
    public function setTrace($cat) {
      $this->cat= $cat;
    }
 
    /**
     * Send XML-RPC message
     *
     * @param   webservices.xmlrpc.XmlRpcMessage message
     * @return  scriptlet.HttpScriptletResponse
     */
    public function send($message) { }
    
    /**
     * Retrieve a XML-RPC message.
     *
     * @param   webservices.xmlrpc.XmlRpcResponse response
     * @return  webservices.xmlrpc.XmlRpcMessage
     */
    public function retrieve($response) { }    

  } 
?>
