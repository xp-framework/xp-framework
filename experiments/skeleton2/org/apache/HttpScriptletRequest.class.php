<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('org.apache.Cookie');

  /**
   * Defines the request sent by the client to the server
   *
   * An instance of this object is passed to the do* methods by
   * the <pre>process</pre> method.
   *
   * @see      xp://org.apache.HttpScriptlet
   * @purpose  Wrap request
   */  
  class HttpScriptletRequest extends Object {
    public
      $headers=         array(),
      $params=          array(),
      $data=            '',
      $method=          HTTP_METHOD_GET,
      $session=         NULL;
      
    /**
     * Retrieves the session or NULL if none exists
     *
     * @access  public
     * @return  &org.apache.HttpSession session object
     */
    public function getSession() {
      return $this->session;
    }

    /**
     * Returns whether a session exists
     *
     * @access  public
     * @return  bool
     */
    public function hasSession() {
      return $this->session != NULL;
    }
    
    /**
     * Sets session
     *
     * @access  public
     * @param   &org.apache.HttpSession session
     */
    public function setSession($s) {
      $this->session= $s;
    }

    /**
     * Returns environment value
     *
     * @access  public
     * @param   string name
     * @return  string
     */
    public function getEnvValue($name) {
      return getenv($name);
    }

    /**
     * Retrieve all cookies
     *
     * @access  public
     * @return  peer.http.Cookie[]
     */
    public function getCookies() {
      $r= array();
      foreach (array_keys($_COOKIE) as $name) {
        $r[]= new Cookie($name, $_COOKIE[$name]);
      }
      return $r;
    }
    
    /**
     * Check whether a cookie exists by a specified name
     *
     * <code>
     *   if ($request->hasCookie('username')) {
     *     with ($c= $request->getCookie('username')); {
     *       $response->write('Welcome back, '.$c->getValue());
     *     }
     *   }
     * </code>
     *
     * @access  public
     * @param   string name
     * @return  bool
     */
    public function hasCookie($name) {
      return isset($_COOKIE[$name]);
    }

    /**
     * Retrieve cookie by it's name
     *
     * @access  public
     * @param   mixed default default NULL the default value if cookie is non-existant
     * @return  &peer.http.Cookie
     */
    public function getCookie($name, $default= NULL) {
      if (isset($_COOKIE[$name])) return new Cookie($name, $_COOKIE[$name]); else return $default;
    }

    /**
     * Returns a request header by its name or NULL if there is no such header
     * Typical request headers are: Accept, Accept-Charset, Accept-Encoding,
     * Accept-Language, Connection, Host, Keep-Alive, Referer, User-Agent
     *
     * @access  public
     * @param   string name Header
     * @param   mixed default default NULL the default value if header is non-existant
     * @return  string Header value
     */
    public function getHeader($name, $default= NULL) {
      $name= strtolower($name);
      if (isset($this->headers[$name])) return $this->headers[$name]; else return $default;
    }
    
    /**
     * Returns a request variable by its name or NULL if there is no such
     * request variable
     *
     * @access  public
     * @param   string name Parameter name
     * @param   mixed default default NULL the default value if parameter is non-existant
     * @return  string Parameter value
     */
    public function getParam($name, $default= NULL) {
      $name= strtolower(strtr($name, '. ', '__'));
      if (isset($this->params[$name])) return $this->params[$name]; else return $default;
    }

    /**
     * Returns whether the specified request variable is set
     *
     * @access  public
     * @param   string name Parameter name
     * @return  bool
     */
    public function hasParam($name) {
      return isset($this->params[strtolower(strtr($name, '. ', '__'))]);
    }
    
    /**
     * Sets request's URI
     *
     * @access  public
     * @param   uri URI a uri parsed by parse_url()
     * @see     php://parse_url
     */
    public function setURI($uri) {
      $this->uri= $uri;
    }
    
    /**
     * Retrieves the requests absolute URI as an uri (which consists
     * of one or more of the following attributes: scheme, host, port,
     * user, pass, path, query and fragment).
     *
     * @access  public
     * @return  uri URI
     */
    public function getURI() {
      return $this->uri;
    }
    
    /**
     * Retrieves session id from request parameters
     *
     * @access  public
     * @return  string session's id
     */
    public function getSessionId() {
      return self::getParam('psessionid');
    }
    
    /**
     * Sets request parameters
     *
     * @access  public
     * @param   &array params
     */
    public function setParams($params) {
      $this->params= $params;
    }

    /**
     * Gets all request parameters
     *
     * @access  public
     * @return  array params
     */
    public function getParams() {
      return $this->params;
    }
    
    /**
     * Sets request data.
     *
     * @access  public
     * @param   &string data
     * @see     xp://org.apache.HttpScriptlet#_handleMethod
     */
    public function setData($data) {
      $this->data= $data;
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
    public function getData() {
      return $this->data;
    }
  }
?>
