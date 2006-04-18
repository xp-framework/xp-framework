<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('org.json.rpc.JsonRpcRouter');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class JsonRpcRouterMock extends JsonRpcRouter {
    var
      $headers= array(),
      $method=  '',
      $params=  array(),
      $data=    '';
      
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function _setupRequest(&$request) {
      $request->headers= array_change_key_case($this->headers, CASE_LOWER);
      $request->method= $this->method;
      $request->setParams(array_change_key_case($this->params, CASE_LOWER));
      $request->setURI(new URL('unittest://'.$this->getClassName().'/'));
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
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
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setMockHeaders($h) {
      $this->headers= $h;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setMockMethod($m) {
      $this->method= $m;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setMockParams($p) {
      $this->params= $p;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setMockData($d) {
      $this->data= $d;
    }
    
    
  }
?>
