<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'peer.http.HttpRequest',
    'io.Stream'
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
    
      // FIXME: This is a hack: an io.Stream does actually have nothing
      // in common with peer.Socket - only most of the methods are named
      // same.
      $s= &new Stream();
      $s->open(STREAM_MODE_READWRITE);
      $s->write($this->_response);
      $s->rewind();
      
      return new HttpResponse($s);
    }
  
  }
?>
