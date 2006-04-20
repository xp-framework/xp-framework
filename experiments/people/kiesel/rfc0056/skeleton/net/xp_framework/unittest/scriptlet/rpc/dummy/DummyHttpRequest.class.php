<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'peer.http.HttpRequest',
    'net.xp_framework.unittest.scriptlet.rpc.dummy.DummySocket'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class DummyHttpRequest extends HttpRequest {
    var
      $_response= '';
      
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
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
