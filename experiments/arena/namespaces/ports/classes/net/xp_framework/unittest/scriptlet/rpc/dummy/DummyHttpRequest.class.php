<?php
/* This class is part of the XP framework
 *
 * $Id: DummyHttpRequest.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace net::xp_framework::unittest::scriptlet::rpc::dummy;

  ::uses(
    'peer.http.HttpRequest',
    'peer.http.HttpResponse',
    'net.xp_framework.unittest.scriptlet.rpc.dummy.DummySocket'
  );

  /**
   * Dummy HTTP request
   *
   * @purpose  Unittesting dummy
   */
  class DummyHttpRequest extends peer::http::HttpRequest {
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
      return new peer::http::HttpResponse(new DummySocket($this->_response));
    }
  }
?>
