<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('scriptlet.Cookie', 'peer.http.HttpConstants', 'scriptlet.HttpScriptletURL');

  /**
   * Defines the request sent by the client to the server
   *
   * An instance of this object is passed to the do* methods by
   * the <pre>process</pre> method.
   *
   * @test     xp://net.xp_framework.unittest.scriptlet.HttpScriptletRequestTest
   * @see      xp://scriptlet.HttpScriptlet
   * @purpose  Wrap request
   */  
  class HttpScriptletRequest extends Object {
    public
      $url=             NULL,
      $env=             array(),
      $headers=         array(),
      $params=          array(),
      $data=            NULL,
      $method=          HttpConstants::GET,
      $session=         NULL;
    
    /**
     * Initialize this request object. Does nothing in this default 
     * implementation, nevertheless, it is a good idea to call 
     * parent::initialize() if you override this method.
     *
     */
    public function initialize() {
    }
    
    /**
     * Retrieves the session or NULL if none exists
     *
     * @return  scriptlet.Session session object
     */
    public function getSession() {
      return $this->session;
    }

    /**
     * Returns whether a session exists
     *
     * @return  bool
     */
    public function hasSession() {
      return $this->session != NULL;
    }
    
    /**
     * Sets session
     *
     * @param   scriptlet.Session session
     */
    public function setSession($s) {
      $this->session= $s;
    }

    /**
     * Returns environment value or the value of default if the 
     * specified environment value cannot be found
     *
     * @param   string name
     * @param   mixed default default NULL
     * @return  string
     */
    public function getEnvValue($name, $default= NULL) {
      if (!isset($this->env[$name])) {
        if (FALSE === ($e= getenv($name))) return $default;
        $this->env[$name]= $e;
      }
      return $this->env[$name];
    }

    /**
     * Retrieve all cookies
     *
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
     * @param   string name
     * @return  bool
     */
    public function hasCookie($name) {
      return isset($_COOKIE[$name]);
    }

    /**
     * Retrieve cookie by it's name
     *
     * @param   mixed default default NULL the default value if cookie is non-existant
     * @return  peer.http.Cookie
     */
    public function getCookie($name, $default= NULL) {
      if (isset($_COOKIE[$name])) return new Cookie($name, $_COOKIE[$name]); else return $default;
    }

    /**
     * Returns a request header by its name or NULL if there is no such header
     * Typical request headers are: Accept, Accept-Charset, Accept-Encoding,
     * Accept-Language, Connection, Host, Keep-Alive, Referer, User-Agent
     *
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
     * @param   string name Parameter name
     * @return  bool
     */
    public function hasParam($name) {
      return isset($this->params[strtolower(strtr($name, '. ', '__'))]);
    }

    /**
     * Sets a request parameter
     *
     * @param   string name Parameter name
     * @param   mixed value
     */
    public function setParam($name, $value) {
      $this->params[$name]= $value;
    }
    
    /**
     * Sets request's URL
     *
     * @param   scriptlet.HttpScriptletURL url
     */
    public function setURL(HttpScriptletURL $url) {
      $this->url= $url;
      $this->setSessionId($this->url->getSessionId());
    }

    /**
     * Sets request's URI
     *
     * @param   peer.URL uri
     */
    #[@deprecated]
    public function setURI($uri) {
      $this->setURL(new HttpScriptletURL($uri->getURL()));
    }
    
    /**
     * Retrieves the requests absolute URI as an URL object
     *
     * @return  string
     */
    #[@deprecated]
    public function getURI() {
      return $this->url->_info;     // HACK
    }

    /**
     * Retrieves the requests absolute URI as an URL object
     *
     * @return  scriptlet.HttpScriptletURL
     */
    public function getURL() {
      return $this->url;
    }
    
    /**
     * Set session id
     *
     * @param  string sessionId session's id
     */
    public function setSessionId($sessionId) {
      return $this->setParam('psessionid', $sessionId);
    }
    
    /**
     * Retrieves session id from request parameters
     *
     * @return  string session's id
     */
    public function getSessionId() {
      return $this->getParam('psessionid');
    }
    
    /**
     * Sets request parameters
     *
     * @param   array<string, string> params
     */
    public function setParams($params) {
      $this->params= array_change_key_case($params, CASE_LOWER);
    }

    /**
     * Gets all request headers
     *
     * @return  array<string, string> headers
     */
    public function getHeaders() {
      return $this->headers;
    }

    /**
     * Sets request headers
     *
     * @param   array<string, string> headers
     */
    public function setHeaders($headers) {
      $this->headers= array_change_key_case($headers, CASE_LOWER);
    }

    /**
     * Add single header - overwrites header if already set
     *
     * @param   string name
     * @param   string value
     */
    public function addHeader($name, $value) {
      $this->headers[strtolower($name)]= $value;
    }

    /**
     * Gets all request parameters
     *
     * @return  array<string, string> params
     */
    public function getParams() {
      return $this->params;
    }
    
    /**
     * Sets request data.
     *
     * @param   string data
     * @see     xp://scriptlet.HttpScriptlet#_handleMethod
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
     * @return  string data
     */
    public function getData() {
      if (NULL === $this->data) {
        $fd= fopen('php://input', 'r');
        $this->data= '';
        while (!feof($fd)) {
          $this->data.= fread($fd, 1024);
        }
        fclose($fd);
      }
      return $this->data;
    }
    
    /**
     * Returns the query string from its environment variable 
     * QUERY_STRING, decoding it if necessary.
     *
     * @return  string
     */
    public function getQueryString() {
      return urldecode($this->getEnvValue('QUERY_STRING'));
    }
    
    /**
     * Retrieve request content type
     *
     * @return  string
     */
    public function getContentType() {
      return $this->getHeader('Content-Type');
    }
    
    /**
     * Returns whether this request contains multipart data (file uploads)
     *
     * @return  bool
     */
    public function isMultiPart() {
      return (bool)strstr($this->getHeader('Content-Type'), 'multipart/form-data');
    }
  }
?>
