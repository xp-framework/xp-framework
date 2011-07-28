<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * HTTP request adapter interface
   *
   */
  interface HttpRequestAdapter {
    
    /**
     * Constructor
     * 
     * @param scriptlet.http.HttpRequest request The request
     */
    public function __construct($request);
    
    /**
     * Retrieve method
     * 
     * @return string 
     */
    public function getMethod();
    
    /**
     * Retrieve header
     * 
     * @param string name The header name
     * @return string
     */
    public function getHeader($name);
    
    /**
     * Retrieve path
     * 
     * @return string
     */
    public function getPath();
    
    /**
     * Retrieve parameter from path
     * 
     * @param string name The parameter name
     * @return string
     */
    public function getParam($name);
    
    /**
     * Retrieve body
     * 
     * @return lang.Object
     */
    public function getData();
  }
?>
