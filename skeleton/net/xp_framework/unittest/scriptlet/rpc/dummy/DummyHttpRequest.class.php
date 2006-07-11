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
    var
      $_response= '';
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string data
     */
    function setResponse($data) {
      $this->_response= $data;
    }    
    
    /**
     * Send request
     *
     * @access  public
     * @return  &peer.http.HttpResponse response object
     */
    function &send($timeout= 60) {
      return new HttpResponse(new DummySocket($this->_response));
    }
  }
?>
