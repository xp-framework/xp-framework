<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('peer.http.HttpTransport', 'peer.http.HttpProxy', 'peer.URL');
  
  /**
   * HTTP connection
   *
   * <code>
   *   $c= new HttpConnection('http://xp-framework.net/');
   *   $response= $c->get(
   *     array('a' => 'b'),
   *     array(
   *       new Header('X-Binford', '6100 (more power)'),
   *       new BasicAuthorization('baz', 'bar'),
   *       'Cookie' => 'username=fred; lastvisit=2004-01-10'
   *     )
   *   );
   *   Console::writeLine('Headers: ', $response);
   *   
   *   while ($chunk= $response->readData()) {
   *     // ...
   *   }
   * </code>
   *
   * @see      rfc://2616
   * @test     xp://net.xp_framework.unittest.peer.HttpTest
   * @purpose  Provide
   */
  class HttpConnection extends Object {
    protected
      $url          = NULL,
      $transport    = NULL,
      $_ctimeout    = 2.0,
      $_timeout     = 60;

    /**
     * Constructor
     *
     * @param   mixed url a string or a peer.URL object
     */
    public function __construct($url) {
      $this->url= $url instanceof URL ? $url : new URL($url);
      $this->transport= HttpTransport::transportFor($this->url);
    }

    /**
     * Set proxy
     *
     * @param   peer.http.HttpProxy proxy
     */
    public function setProxy(HttpProxy $proxy) {
      $this->transport->setProxy($proxy);
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
     * Get URL
     *
     * @return  peer.URL
     */
    public function getUrl() {
      return $this->url;
    }
    
    /**
     * Returns a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        '%s(->URL{%s via %s}, timeout: [read= %.2f, connect= %.2f])',
        $this->getClassName(),
        $this->url->getUrl(),
        $this->transport->toString(),
        $this->_timeout,
        $this->_ctimeout
      );
    }
    
    /**
     * Send a HTTP request
     *
     * @param   peer.http.HttpRequest
     * @return  peer.http.HttpResponse response object
     */
    public function send(HttpRequest $r) {
      return $this->transport->send($r, $this->_timeout, $this->_ctimeout);
    }

    /**
     * Creates a new HTTP request. For use in conjunction with send(), e.g.:
     *
     * <code>
     *   $conn= new HttpConnection('http://example.com/');
     *   
     *   with ($request= $conn->create(new HttpRequest())); {
     *     $request->setMethod(HTTP_GET);
     *     $request->setParameters(array('a' => 'b'));
     *     $request->setHeader('X-Binford', '6100 (more power)');
     *
     *     $response= $conn->send($request);
     *     // ...
     *   }
     * </code>
     *
     * @param   peer.http.HttpRequest
     * @return  peer.http.HttpRequest request object
     */
    public function create(HttpRequest $r) {
      $r->setUrl($this->url);
      return $r;
    }
    
    /**
     * Perform any request
     *
     * @param   string method request method, e.g. HTTP_GET
     * @param   mixed parameters
     * @param   array headers default array()
     * @return  peer.http.HttpResponse response object
     * @throws  io.IOException
     */
    public function request($method, $parameters, $headers= array()) {
      $r= new HttpRequest($this->url);
      $r->setMethod($method);
      $r->setParameters($parameters);
      $r->addHeaders($headers);
      return $this->send($r);
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
     * Perform a PUT request
     *
     * @param   string arg default NULL
     * @param   array headers default array()
     * @return  peer.http.HttpResponse response object
     */
    public function put($arg= NULL, $headers= array()) {
      return $this->request(HTTP_PUT, $arg, $headers);
    }

    /**
     * Perform a DELETE request
     *
     * @param   string arg default NULL
     * @param   array headers default array()
     * @return  peer.http.HttpResponse response object
     */
    public function delete($arg= NULL, $headers= array()) {
      return $this->request(HTTP_DELETE, $arg, $headers);
    }

    /**
     * Perform an OPTIONS request
     *
     * @param   string arg default NULL
     * @param   array headers default array()
     * @return  peer.http.HttpResponse response object
     */
    public function options($arg= NULL, $headers= array()) {
      return $this->request(HTTP_OPTIONS, $arg, $headers);
    }

    /**
     * Perform a TRACE request
     *
     * @param   string arg default NULL
     * @param   array headers default array()
     * @return  peer.http.HttpResponse response object
     */
    public function trace($arg= NULL, $headers= array()) {
      return $this->request(HTTP_TRACE, $arg, $headers);
    }
  }
?>
