<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class HttpScriptletEnvironment extends Object {
    protected
      $environment  = array(),
      $headers      = array(),
      $cookies      = array(),
      $params       = array(),
      $files        = array(),
      $data         = '';

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public static function instanciate(ServerAPI $api) {
      $env= new self();
      
      $env->environment= array_change_key_case($api->getEnvValues(), CASE_LOWER);
      $env->cookies= $api->getCookies();
      $env->params= $api->getRequestParameters();
      $env->files= $api->getUploadFiles();

      with ($stream= $api->getStdinStream()); {
        while (!$stream->eof()) { $env->data.= $stream->read(4096); }
      }
      
      return $env;
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
      return (isset($this->environment[$name]) ? $this->environment[$name] : $default);
    }
    
    /**
     * Retrieve all cookies
     *
     * @return  peer.http.Cookie[]
     */
    public function getCookies() {
      $r= array();
      foreach ($this->cookies as $name => $value) {
        $r[]= new Cookie($name, $value);
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
     * @param   string name
     * @return  bool
     */
    public function hasCookie($name) {
      return isset($this->cookies[$name]);
    }

    /**
     * Retrieve cookie by it's name
     *
     * @param   mixed default default NULL the default value if cookie is non-existant
     * @return  peer.http.Cookie
     */
    public function getCookie($name, $default= NULL) {
      if (isset($this->cookies[$name])) return new Cookie($name, $this->cookies[$name]); else return $default;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function getFiles() {
      return $this->files;
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
      if (isset($this->headers[$name])) return $this->headers[$name]; else return $default;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function hasParam($name) {
      return isset($this->params[$name]);
    }    
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function getParam($name, $default= NULL) {
      return (isset($this->params[$name]) ? $this->params[$name] : $default);
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function setParam($name, $value) {
      $this->params[$name]= $value;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function getData() {
      return $this->data;
    }
  }
?>
