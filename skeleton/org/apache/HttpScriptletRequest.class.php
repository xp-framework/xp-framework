<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Defines the request sent by the client to the server
   *
   * An instance of this object is passed to the do* methods by
   * the <pre>process</pre> method.
   *
   * @see   xp://org.apache.HttpScriptlet
   */  
  class HttpScriptletRequest extends Object {
    var
      $headers=         array(),
      $params=          array(),
      $data=            '',
      $method=          HTTP_METHOD_GET,
      $session=         NULL;
      
    /**
     * Retreives the session or NULL if none exists
     *
     * @access  public
     * @return  lang.Object session object
     */
    function &getSession() {
      return $this->session;
    }
    
    /**
     * Sets session
     *
     * @access  public
     * @param   lang.Object session
     */
    function setSession(&$s) {
      $this->session= &$s;
    }

    /**
     * Gibt eine Umgebungsvariable zurücke
     *
     * @access  public
     * @param   string name Header
     * @return  string Header-Wert
     */
    function getEnvValue($name) {
      return getenv($name);
    }
      
    /**
     * Returns a request header by its name or NULL if there is no such header
     * Typical request headers are: Accept, Accept-Charset, Accept-Encoding,
     * Accept-Language, Connection, Host, Keep-Alive, Referer, User-Agent
     *
     * @access  public
     * @param   string name Header
     * @return  string Header value
     */
    function getHeader($name) {
      $name= strtolower($name);
      if (isset($this->headers[$name])) return $this->headers[$name]; else return NULL;
    }
    
    /**
     * Returns a request variable by its name or NULL if there is no such
     * request variable
     *
     * @access  public
     * @param   string name Parameter name
     * @return  string Parameter value
     */
    function getParam($name) {
      $name= strtolower($name);
      if (isset($this->params[$name])) return $this->params[$name]; else return NULL;
    }

    /**
     * Returns whether the specified request variable is set
     *
     * @access  public
     * @param   string name Parameter name
     * @return  bool
     */
    function hasParam($name) {
      return isset($this->params[strtolower($name)]);
    }
    
    /**
     * Sets request's URI
     *
     * @access  public
     * @param   uri URI a uri parsed by parse_url()
     * @see     php://parse_url
     */
    function setURI($uri) {
      $this->uri= $uri;
    }
    
    /**
     * Retreives the requests absolute URI as an uri (which consists
     * of one or more of the following attributes: scheme, host, port,
     * user, pass, path, query and fragment).
     *
     * @access  public
     * @return  uri URI
     */
    function getURI() {
      return $this->uri;
    }
    
    /**
     * Retreives session id from request parameters
     *
     * @access  public
     * @return  string session's id
     */
    function getSessionId() {
      return $this->getParam('psessionid');
    }
    
    /**
     * Sets request parameters
     *
     * @access  public
     * @param   &array params
     */
    function setParams(&$params) {
      $this->params= &$params;
    }
    
    /**
     * Sets request data.
     *
     * @access  public
     * @param   &string data
     * @see     xp://org.apache.HttpScriptlet#_handleMethod
     */
    function setData(&$data) {
      $this->data= &$data;
    }
    
    /**
     * Returns request data - for GET requests, this is the equivalent to
     * the environment variable QUERY_STRING, for POST request it is
     * the equivalent to the raw post data.
     *
     * This is especially useful for the SOAP implementation where the
     * entire request body resembles the SOAP message (no parameters).
     *
     * @access  public
     * @return  &string data
     */
    function &getData() {
      return $this->data;
    }
  }
?>
