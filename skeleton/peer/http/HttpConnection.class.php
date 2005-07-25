<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('peer.http.HttpRequestFactory');
  
  /**
   * HTTP connection
   *
   * <code>
   *   $c= &new HttpConnection('http://xp-framework.net/');
   *   try(); {
   *     $response= &$c->get(
   *       array('a' => 'b'),
   *       array(
   *         new Header('X-Binford', '6100 (more power)'),
   *         new BasicAuthorization('baz', 'bar'),
   *         'Cookie' => 'username=fred; lastvisit=2004-01-10'
   *       )
   *     );
   *     while ($buf= $response->readData()) {
   *       var_dump($buf);
   *       flush();
   *     }
   *   } if (catch('IOException', $e)) {
   *     $e->printStackTrace();
   *     exit();
   *   } if (catch('Exception', $e)) {
   *     $e->printStackTrace();
   *     exit();
   *   }
   *   
   *   var_dump($response);
   * </code>
   *
   * @see      rfc://2616
   * @purpose  Provide
   */
  class HttpConnection extends Object {
    var 
      $request     = NULL,
      $response    = NULL,
      $auth        = NULL;
    
    var
      $_timeout    = 60;

    /**
     * Constructor
     *
     * @access  public
     * @param   mixed url a string or a peer.URL object
     */
    function __construct($url) {
      if (!is_a($url, 'URL')) $url= &new URL($url);
      $this->_createRequest($url);
      
    }
    
    /**
     * Create the request object
     *
     * @access  protected
     * @param   &peer.URL object
     */
    function _createRequest(&$url) {
      $this->request= &HttpRequestFactory::factory($url);
    }

    /**
     * Set timeout
     *
     * @access  public
     * @param   mixed imeout
     */
    function setTimeout($timeout) {
      $this->_timeout= $timeout;
    }

    /**
     * Get timeout
     *
     * @access  public
     * @return  mixed
     */
    function getTimeout() {
      return $this->_timeout;
    }
    
    /**
     * Perform any request
     *
     * @access  public
     * @param   string method request method, e.g. HTTP_GET
     * @param   mixed arg
     * @param   array headers default array()
     * @return  &peer.http.HttpResponse response object
     * @throws  io.IOException
     * @throws  lang.IllegalAccessException
     */
    function &request($method, $arg, $headers= array()) {
      if (!$this->request) return throw(new IllegalAccessException(
        'No request object returned from HttpRequestFactory::factory'
      ));
      
      $this->request->setMethod($method);
      $this->request->setParameters($arg);
      $this->request->addHeaders($headers);
      
      try(); {
        $this->response= &$this->request->send($this->_timeout);
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      
      return $this->response;
    }

    /**
     * Perform a GET request
     *
     * @access  public
     * @param   mixed arg default NULL
     * @param   array headers default array()
     * @return  &peer.http.HttpResponse response object
     */
    function &get($arg= NULL, $headers= array()) {
      return $this->request(HTTP_GET, $arg, $headers);
    }
    
    /**
     * Perform a HEAD request
     *
     * @access  public
     * @param   mixed arg default NULL
     * @param   array headers default array()
     * @return  &peer.http.HttpResponse response object
     */
    function &head($arg= NULL, $headers= array()) {
      return $this->request(HTTP_HEAD, $arg, $headers);
    }
    
    /**
     * Perform a POST request
     *
     * @access  public
     * @param   mixed arg default NULL
     * @param   array headers default array()
     * @return  &peer.http.HttpResponse response object
     */
    function &post($arg= NULL, $headers= array()) {
      return $this->request(HTTP_POST, $arg, $headers);
    }
    
    /**
     * Perform a Put request
     *
     * @access  public
     * @param   string arg default NULL
     * @param   array headers default array()
     * @return  &peer.http.HttpResponse response object
     */
    function &put($arg= NULL, $headers= array()) {
      return $this->request(HTTP_PUT, new RequestData($arg), $headers);
    }
  }
?>
