<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  namespace net::xp_framework::unittest::scriptlet::rpc::mock;

  ::uses('webservices.soap.rpc.SoapRpcRouter');

  /**
   * Mock class
   *
   * @purpose  Mock
   */
  class SoapRpcRouterMock extends webservices::soap::rpc::SoapRpcRouter {
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
      $request->headers= array_change_key_case($this->headers, CASE_LOWER);
      $request->method= $this->method;
      $request->setParams(array_change_key_case($this->params, CASE_LOWER));
      $request->setURI(new peer::URL('unittest://'.$this->getClassName().'/'));
    }
    
    /**
     * Handle method.
     *
     * @param   &scriptlet.HttpRequest request
     * @return  string
     */
    public function handleMethod($request) {
      switch ($request->method) {
        case HTTP_POST:
          $request->setData($this->data);
          $m= 'doPost';
          break;
          
        case HTTP_GET:
          $request->setData($this->data);
          $m= 'doGet';
          break;
          
        case HTTP_HEAD:
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
