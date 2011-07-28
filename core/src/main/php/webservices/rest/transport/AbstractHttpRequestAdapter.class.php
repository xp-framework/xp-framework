<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('webservices.rest.transport.HttpRequestAdapter');
  
  /**
   * Abstract basic implementation of adapter
   *
   * @purpose Adapter
   */
  abstract class AbstractHttpRequestAdapter extends Object implements HttpRequestAdapter {
    protected $request= NULL;
    
    /**
     * Constructor
     * 
     * @param scriptlet.HttpRequest request The request
     */
    public function __construct($request) {
      $this->request= $request;
    }
    
    /**
     * Retrieve method
     * 
     * @return string 
     */
    public function getMethod() {
      return $this->request->getMethod();
    }
    
    /**
     * Retrieve header
     * 
     * @param string name The header name
     * @return string
     */
    public function getHeader($name) {
      return $this->request->getHeader($name);
    }
    
    /**
     * Retrieve path
     * 
     * @return string
     */
    public function getPath() {
      return $this->request->getURL()->getPath();
    }
    
    /**
     * Retrieve parameter
     * 
     * @param string name The parameter name
     * @return string
     */
    public function getParam($name) {
      return $this->request->getParam($name);
    }
  }
?>
