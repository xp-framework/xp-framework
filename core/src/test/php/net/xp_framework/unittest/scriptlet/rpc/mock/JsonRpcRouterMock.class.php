<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.System', 'webservices.json.rpc.JsonRpcRouter');

  /**
   * Mock class
   *
   * @purpose  Mock
   */
  class JsonRpcRouterMock extends JsonRpcRouter {
    public
      $headers= array(),
      $method=  '',
      $params=  array(),
      $data=    '';
      
    /**
     * Set the request from the environment.
     *
     * @param   &scriptlet.HttpRequest request
     */
    protected function _setupRequest($request) {
      $request->env['SERVER_PROTOCOL']= 'HTTP/1.1';
      $request->env['REQUEST_METHOD']= $this->method;
      $request->env['HTTP_HOST']= 'unittest';
      $request->env['REQUEST_URI']= '/';
      $request->env['QUERY_STRING']= '';
      $request->method= $this->method;
      $request->setHeaders($this->headers);
      $request->setParams($this->params);
    }
    
    /**
     * Handle method.
     *
     * @param   &scriptlet.HttpRequest request
     * @return  string
     */
    public function handleMethod($request) {
      switch ($request->method) {
        case HttpConstants::POST:
          $request->setData($this->data);
          $m= 'doPost';
          break;
          
        case HttpConstants::GET:
          $request->setData($this->data);
          $m= 'doGet';
          break;
          
        case HttpConstants::HEAD:
          $request->setData($this->data);
          $m= 'doHead';
          break;        
          
        default:
          $m= NULL;
      }
      
      return $m;
    }    
    
    /**
     * Get Params
     *
     * @return  mixed[]
     */
    public function getParams() {
      return $this->params;
    }

    /**
     * Set Headers
     *
     * @param   mixed[] headers
     */
    public function setMockHeaders($h) {
      $this->headers= $h;
    }
    
    /**
     * Set method
     *
     * @param   string m
     */
    public function setMockMethod($m) {
      $this->method= $m;
    }
    
    /**
     * Set Params
     *
     * @param   mixed[] params
     */
    public function setMockParams($p) {
      $this->params= $p;
    }
    
    /**
     * Set Data
     *
     * @param   string data
     */
    public function setMockData($data) {
      $this->data= $data;
    }
    
    
  }
?>
