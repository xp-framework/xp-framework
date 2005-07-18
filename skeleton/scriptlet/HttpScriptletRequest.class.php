<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('scriptlet.Cookie', 'peer.http.HttpConstants');

  /**
   * Defines the request sent by the client to the server
   *
   * An instance of this object is passed to the do* methods by
   * the <pre>process</pre> method.
   *
   * @see      xp://scriptlet.HttpScriptlet
   * @purpose  Wrap request
   */  
  class HttpScriptletRequest extends Object {
    var
      $headers=         array(),
      $params=          array(),
      $data=            '',
      $method=          HTTP_GET,
      $session=         NULL;
    
    /**
     * Initialize this request object. Does nothing in this default 
     * implementation, nevertheless, it is a good idea to call 
     * parent::initialize() if you override this method.
     *
     * @access  public
     */
    function initialize() {
    }
    
    /**
     * Retrieves the session or NULL if none exists
     *
     * @access  public
     * @return  &scriptlet.HttpSession session object
     */
    function &getSession() {
      return $this->session;
    }

    /**
     * Returns whether a session exists
     *
     * @access  public
     * @return  bool
     */
    function hasSession() {
      return $this->session != NULL;
    }
    
    /**
     * Sets session
     *
     * @access  public
     * @param   &scriptlet.HttpSession session
     */
    function setSession(&$s) {
      $this->session= &$s;
    }

    /**
     * Returns environment value or the value of default if the 
     * specified environment value cannot be found
     *
     * @access  public
     * @param   string name
     * @param   mixed default default NULL
     * @return  string
     */
    function getEnvValue($name, $default= NULL) {
      return (FALSE === ($e= getenv($name))) ? $default : $e;
    }

    /**
     * Retrieve all cookies
     *
     * @access  public
     * @return  peer.http.Cookie[]
     */
    function getCookies() {
      $r= array();
      foreach (array_keys($_COOKIE) as $name) {
        $r[]= &new Cookie($name, $_COOKIE[$name]);
      }
      return $r;
    }
    
    /**
     * Check whether a cookie exists by a specified name
     *
     * <code>
     *   if ($request->hasCookie('username')) {
     *     with ($c= &$request->getCookie('username')); {
     *       $response->write('Welcome back, '.$c->getValue());
     *     }
     *   }
     * </code>
     *
     * @access  public
     * @param   string name
     * @return  bool
     */
    function hasCookie($name) {
      return isset($_COOKIE[$name]);
    }

    /**
     * Retrieve cookie by it's name
     *
     * @access  public
     * @param   mixed default default NULL the default value if cookie is non-existant
     * @return  &peer.http.Cookie
     */
    function &getCookie($name, $default= NULL) {
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
    function getHeader($name, $default= NULL) {
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
    function getParam($name, $default= NULL) {
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
    function hasParam($name) {
      return isset($this->params[strtolower(strtr($name, '. ', '__'))]);
    }

    /**
     * Sets a request parameter
     *
     * @access  public
     * @param   string name Parameter name
     * @param   mixed value
     */
    function setParam($name, $value) {
      $this->params[$name]= $value;
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
     * Retrieves the requests absolute URI as an uri (which consists
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
     * Retrieves session id from request parameters
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
     * Gets all request parameters
     *
     * @access  public
     * @return  array params
     */
    function getParams() {
      return $this->params;
    }
    
    /**
     * Sets request data.
     *
     * @access  public
     * @param   &string data
     * @see     xp://scriptlet.HttpScriptlet#_handleMethod
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
    
    /**
     * Returns the query string from its environment variable 
     * QUERY_STRING, decoding it if necessary.
     *
     * @access  public
     * @return  string
     */
    function getQueryString() {
      return urldecode(getenv('QUERY_STRING'));
    }
    
    /**
     * Retrieve request content type
     *
     * @access  public
     * @return  string
     */
    function getContentType() {
      return $this->getHeader('Content-Type');
    }
    
    /**
     * Returns whether this request contains multipart data (file uploads)
     *
     * @access  public
     * @return  bool
     */
    function isMultiPart() {
      return (bool)strstr($this->getHeader('Content-Type'), 'multipart/form-data');
    }
  }
?>
