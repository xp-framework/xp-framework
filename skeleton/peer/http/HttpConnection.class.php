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
   *   $c= &new HttpConnection('http://xp.php3.de/');
   *   try(); {
   *     $response= &$c->get(
   *       array('a' => 'b'),
   *       array(
   *         new Header('X-Binford', '6100 (more power)'),
   *         new Cookie('test', 'foo'),
   *         new BasicAuthorization('baz', 'bar')
   *       )
   *     );
   *     while (FALSE !== ($buf= $response->readData())) {
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

    /**
     * Constructor
     *
     * @access  public
     * @param   mixed url a string or a peer.URL object
     */
    function __construct($url) {
      $this->request= &HttpRequestFactory::factory($url);
      parent::__construct();
    }
    
    /**
     * Perform any request
     *
     * @access  public
     * @param   string method request method, e.g. HTTP_GET
     * @param   mixed arg
     * @param   array headers default array()
     * @return  &peer.http.HttpResponse response object
     * @throws  IOException
     */
    function request($method, $arg, $headers= array()) {
      $this->request->setMethod($method);
      $this->request->setParameters($arg);
      $this->request->addHeaders($headers);
      
      try(); {
        $this->response= &$this->request->send();
      } if (catch('Exception', $e)) {
        throw($e);
      }
      
      return $this->response;
    }

    /**
     * Perform a GET request
     *
     * @access  public
     * @param   mixed arg
     * @param   array headers default array()
     * @return  &peer.http.HttpResponse response object
     */
    function get($arg, $headers= array()) {
      return $this->request(HTTP_GET, $arg, $headers);
    }
    
    /**
     * Perform a HEAD request
     *
     * @access  public
     * @param   mixed arg
     * @param   array headers default array()
     * @return  &peer.http.HttpResponse response object
     */
    function head($arg, $headers= array()) {
      return $this->request(HTTP_HEAD, $arg, $headers);
    }
    
    /**
     * Perform a POST request
     *
     * @access  public
     * @param   mixed arg
     * @param   array headers default array()
     * @return  &peer.http.HttpResponse response object
     */
    function post($arg, $headers= array()) {
      return $this->request(HTTP_POST, $arg, $headers);
    }
  }
?>
