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
   * @test     xp://net.xp_framework.unittest.peer.HttpTest
   * @purpose  Provide
   */
  class HttpConnection extends Object {
    public 
      $request     = NULL,
      $response    = NULL,
      $auth        = NULL;
    
    public
      $_ctimeout    = 2.0,
      $_timeout     = 60;

    /**
     * Constructor
     *
     * @param   mixed url a string or a peer.URL object
     */
    public function __construct($url) {
      if (!is('URL', $url)) $url= new URL($url);
      $this->_createRequest($url);
      
    }
    
    /**
     * Create the request object
     *
     * @param   peer.URL object
     */
    protected function _createRequest($url) {
      $this->request= HttpRequestFactory::factory($url);
    }

    /**
     * Set connect timeout
     *
     * @param   float timeout
     */
    public function setConnectTimeout($timeout) {
      $this->_ctimeout= $timeout;
    }

    /**
     * Get timeout
     *
     * @return  float
     */
    public function getConnectTimeout() {
      return $this->_ctimeout;
    }

    /**
     * Set timeout
     *
     * @param   int timeout
     */
    public function setTimeout($timeout) {
      $this->_timeout= $timeout;
    }

    /**
     * Get timeout
     *
     * @return  int
     */
    public function getTimeout() {
      return $this->_timeout;
    }
    
    /**
     * Perform any request
     *
     * @param   string method request method, e.g. HTTP_GET
     * @param   mixed arg
     * @param   array headers default array()
     * @return  peer.http.HttpResponse response object
     * @throws  io.IOException
     * @throws  lang.IllegalAccessException
     */
    public function request($method, $arg, $headers= array()) {
      if (!$this->request) throw(new IllegalAccessException(
        'No request object returned from HttpRequestFactory::factory'
      ));
      
      $this->request->setMethod($method);
      $this->request->setParameters($arg);
      $this->request->addHeaders($headers);
      
      try {
        $this->response= $this->request->send($this->_timeout, $this->_ctimeout);
      } catch (Exception $e) {
        throw($e);
      }
      
      return $this->response;
    }

    /**
     * Perform a GET request
     *
     * @param   mixed arg default NULL
     * @param   array headers default array()
     * @return  peer.http.HttpResponse response object
     */
    public function get($arg= NULL, $headers= array()) {
      return $this->request(HTTP_GET, $arg, $headers);
    }
    
    /**
     * Perform a HEAD request
     *
     * @param   mixed arg default NULL
     * @param   array headers default array()
     * @return  peer.http.HttpResponse response object
     */
    public function head($arg= NULL, $headers= array()) {
      return $this->request(HTTP_HEAD, $arg, $headers);
    }
    
    /**
     * Perform a POST request
     *
     * @param   mixed arg default NULL
     * @param   array headers default array()
     * @return  peer.http.HttpResponse response object
     */
    public function post($arg= NULL, $headers= array()) {
      return $this->request(HTTP_POST, $arg, $headers);
    }
    
    /**
     * Perform a Put request
     *
     * @param   string arg default NULL
     * @param   array headers default array()
     * @return  peer.http.HttpResponse response object
     */
    public function put($arg= NULL, $headers= array()) {
      return $this->request(HTTP_PUT, new RequestData($arg), $headers);
    }
  }
?>
