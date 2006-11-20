<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'webservices.json.rpc.JsonRpcRequest',
    'webservices.json.rpc.JsonRpcResponse',
    'webservices.json.rpc.JsonResponseMessage',
    'scriptlet.rpc.AbstractRpcRouter'
  );

  /**
   * JSON RPC Router class. You can use this class to implement
   * a JSON webservice.
   *
   * @see      xp://webservices.json.rpc.JsonClient
   * @purpose  JSON-RPC-Service
   */
  class JsonRpcRouter extends AbstractRpcRouter {

    /**
     * Create a request object.
     *
     * @access  protected
     * @return  &scriptlet.rpc.AbstractRpcRequest
     */
    function &_request() {
      return new JsonRpcRequest();
    }

    /**
     * Create a response object.
     *
     * @access  protected
     * @return  &scriptlet.rpc.AbstractRpcResponse
     */
    function &_response() {
      return new JsonRpcResponse();
    }
    
    /**
     * Create a message object.
     *
     * @access  protected
     * @return  &scriptlet.rpc.AbstractRpcMessage
     */
    function &_message() {
      return new JsonResponseMessage();
    }
  }
?>
