<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('webservices.rest.transport.HttpResponseAdapter');
  
  /**
   * Abstract basic implementation of adapter
   *
   * @purpose Adapter
   */
  abstract class AbstractHttpResponseAdapter extends Object implements HttpResponseAdapter {
    protected $response= NULL;
    
    /**
     * Constructor
     * 
     * @param scriptlet.HttpResponse response The response
     */
    public function __construct($response) {
      $this->response= $response;
    }
    
    /**
     * Set status code
     * 
     * @param int code The status code
     */
    public function setStatus($code) {
      $this->response->setStatus($code);
    }
    
    /**
     * Set header
     * 
     * @param string name The header name
     * @param string value The value
     * @return string
     */
    public function setHeader($name, $value) {
      return $this->response->setHeader($name, $value);
    }
  }
?>
