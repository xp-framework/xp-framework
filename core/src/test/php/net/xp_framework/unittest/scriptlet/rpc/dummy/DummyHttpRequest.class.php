<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'peer.http.HttpRequest',
    'peer.http.HttpResponse',
    'net.xp_framework.unittest.scriptlet.rpc.dummy.DummySocket'
  );

  /**
   * Dummy HTTP request
   *
   * @purpose  Unittesting dummy
   */
  class DummyHttpRequest extends HttpRequest {
    public
      $_response= '';
      
    /**
     * Constructor
     *
     * @param   string data
     */
    public function setResponse($data) {
      $this->_response= $data;
    }    
    
    /**
     * Send request
     *
     * @return  &peer.http.HttpResponse response object
     */
    public function send($timeout= 60) {
      return new HttpResponse(new DummySocket($this->_response));
    }
  }
?>
