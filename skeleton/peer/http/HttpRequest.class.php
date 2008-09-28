<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */

  uses(
    'peer.http.HttpConstants',
    'peer.Socket',
    'peer.URL',
    'peer.http.HttpResponse',
    'peer.http.RequestData',
    'peer.Header'
  );
  
  /**
   * Wrap HTTP/1.0 and HTTP/1.1 requests (used internally by the 
   * HttpConnection class)
   *
   * @test     xp://net.xp_framework.unittest.peer.HttpRequestTest
   * @see      xp://peer.http.HttpConnection
   * @see      rfc://2616
   * @purpose  HTTP request
   */
  class HttpRequest extends Object {
    public
      $url        = NULL,
      $method     = HttpConstants::GET,
      $target     = '',
      $version    = HttpConstants::VERSION_1_1,
      $headers    = array('Connection' => 'close'),
      $parameters = array();
      
    /**
     * Constructor
     *
     * @param   peer.URL url object
     */
    public function __construct(URL $url= NULL) {
      if (NULL !== $url) $this->setUrl($url);
    }

    /**
     * Set URL
     *
     * @param   peer.URL url object
     */
    public function setUrl(URL $url) {
      $this->url= $url;
      if ($url->getUser() && $url->getPassword()) {
        $this->headers['Authorization']= 'Basic '.base64_encode($url->getUser().':'.$url->getPassword());
      }
      $port= $this->url->getPort(-1);
      $this->headers['Host']= $this->url->getHost().(-1 == $port ? '' : ':'.$port);
      $this->target= $this->url->getPath('/');
    }

    /**
     * Get URL
     *
     * @return  peer.URL url object
     */
    public function getUrl() {
      return $this->url;
    }

    /**
     * Set request target
     *
     * @param   string target
     */
    public function setTarget($target) {
      $this->target= $target;
    }
    
    /**
     * Set request method
     *
     * @param   string method request method, e.g. HttpConstants::GET
     */
    public function setMethod($method) {
      $this->method= $method;
    }

    /**
     * Set request parameters
     *
     * @param   mixed p either a string, a PostData object or an associative array
     */
    public function setParameters($p) {
      if ($p instanceof RequestData) {
        $this->parameters= $p;
        return;
      } else if (is_string($p)) {
        parse_str($p, $out); 
        $params= $out;
      } else if (is_array($p)) {
        $params= $p;
      } else {
        $params= array();
      }
      
      $this->parameters= array_diff($params, $this->url->getParams());
    }
    
    /**
     * Set header
     *
     * @param   string k header name
     * @param   string v header value
     */
    public function setHeader($k, $v) {
      $this->headers[$k]= $v;
    }

    /**
     * Add headers
     *
     * @param   array headers
     */
    public function addHeaders($headers) {
      foreach ($headers as $key => $header) {
        $this->headers[$header instanceof Header ? $header->getName() : $key] = $header;
      }
    }
    
    /**
     * Get request string
     *
     * @return  string
     */
    public function getRequestString() {
      if ($this->parameters instanceof RequestData) {
        $query= '&'.$this->parameters->getData();
      } else {
        $query= '';
        foreach ($this->parameters as $k => $v) {
          $query.= '&'.$k.'='.urlencode($v);
        }
      }
      $target= $this->target;
      
      // Which HTTP method? GET and HEAD use query string, POST etc. use
      // body for passing parameters
      switch ($this->method) {
        case HttpConstants::HEAD: case HttpConstants::GET: case HttpConstants::DELETE: case HttpConstants::OPTIONS:
          if (NULL !== $this->url->getQuery()) {
            $target.= '?'.$this->url->getQuery().(empty($query) ? '' : $query);
          } else {
            $target.= empty($query) ? '' : '?'.substr($query, 1);
          }
          $body= '';
          break;
          
        case HttpConstants::POST: case HttpConstants::PUT: case HttpConstants::TRACE: default:
          $body= substr($query, 1);
          if (NULL !== $this->url->getQuery()) $target.= '?'.$this->url->getQuery();
          $this->headers['Content-Length']= strlen($body);
          if (empty($this->headers['Content-Type'])) {
            $this->headers['Content-Type']= 'application/x-www-form-urlencoded';
          }
          break;
      }
      
      $request= sprintf(
        "%s %s HTTP/%s\r\n",
        $this->method,
        $target,
        $this->version
      );
      
      // Add request headers
      foreach ($this->headers as $k => $v) {
        $request.= ($v instanceof Header ? $v->toString() : $k.': '.$v)."\r\n";
      }
      
      return $request."\r\n".$body;
    }
  }
?>
