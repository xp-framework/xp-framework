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
     * @return  &scriptlet.rpc.AbstractRpcRequest
     */
    public function _request() {
      return new JsonRpcRequest();
    }

    /**
     * Create a response object.
     *
     * @return  &scriptlet.rpc.AbstractRpcResponse
     */
    public function _response() {
      return new JsonRpcResponse();
    }
    
    /**
     * Create a message object.
     *
     * @return  &scriptlet.rpc.AbstractRpcMessage
     */
    public function _message() {
      return new JsonResponseMessage();
    }
  }
?>
