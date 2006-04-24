<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xml.xmlrpc.rpc.XmlRpcRouter');

  /**
   * Mock class
   *
   * @purpose  Mock
   */
  class XmlRpcRouterMock extends XmlRpcRouter {
    var
      $headers= array(),
      $method=  '',
      $params=  array(),
      $data=    '';
      
    /**
     * Set the request from the environment.
     *
     * @access  protected
     * @param   &scriptlet.HttpRequest request
     */
    function _setupRequest(&$request) {
      $request->headers= array_change_key_case($this->headers, CASE_LOWER);
      $request->method= $this->method;
      $request->setParams(array_change_key_case($this->params, CASE_LOWER));
      $request->setURI(new URL('unittest://'.$this->getClassName().'/'));
    }
    
    /**
     * Handle method.
     *
     * @access  public
     * @param   &scriptlet.HttpRequest request
     * @return  string
     */
    function handleMethod(&$request) {
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
     * @access  public
     * @return  mixed[]
     */
    function getParams() {
      return $this->params;
    }

    /**
     * Set Headers
     *
     * @access  public
     * @param   mixed[] headers
     */
    function setMockHeaders($h) {
      $this->headers= $h;
    }
    
    /**
     * Set method
     *
     * @access  public
     * @param   string m
     */
    function setMockMethod($m) {
      $this->method= $m;
    }
    
    /**
     * Set Params
     *
     * @access  public
     * @param   mixed[] params
     */
    function setMockParams($p) {
      $this->params= $p;
    }
    
    /**
     * Set Data
     *
     * @access  public
     * @param   string data
     */
    function setMockData($data) {
      $this->data= $data;
    }
  }
?>
