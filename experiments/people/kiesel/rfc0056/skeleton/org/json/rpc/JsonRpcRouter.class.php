<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'org.json.rpc.JsonRpcRequest',
    'org.json.rpc.JsonRpcResponse',
    'org.json.rpc.JsonMessage',
    'scriptlet.rpc.AbstractRpcRouter'
  );

  /**
   * JSON RPC Router class. You can use this class to implement
   * a JSON webservice.
   *
   * @see      xp://org.json.JsonClient
   * @purpose  JSON-RPC-Service
   */
  class JsonRpcRouter extends AbstractRpcRouter {

    /**
     * Create a request object.
     *
     * @access  protected
     * @return  &xml.xmlrpc.rpc.XmlRpcRequest
     */
    function &_request() {
      return new JsonRpcRequest();
    }

    /**
     * Create a response object.
     *
     * @access  protected
     * @return  &xml.xmlrpc.rpc.XmlRpcResponse
     */
    function &_response() {
      return new JsonRpcResponse();
    }
    
    /**
     * Create a message object.
     *
     * @access  protected
     * @return  &org.json.rpc.JsonMessage
     */
    function &_message() {
      return new JsonMessage();
    }
  }
?>
