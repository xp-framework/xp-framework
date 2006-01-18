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
   * @see      xp://peer.http.HttpConnection
   * @see      rfc://2616
   * @purpose  HTTP request
   */
  class HttpRequest extends Object {
    var
      $url        = NULL,
      $method     = HTTP_GET,
      $version    = HTTP_VERSION_1_1,
      $headers    = array(
        'Connection' => 'close'
      ),
      $parameters = array();
      
    /**
     * Constructor
     *
     * @access  public
     * @param   &peer.URL url object
     */
    function __construct(&$url) {
      $this->url= &$url;
      if ($url->getUser() && $url->getPassword()) {
        $this->headers['Authorization']= 'Basic '.base64_encode($url->getUser().':'.$url->getPassword());
      }
    }
    
    /**
     * Set request method
     *
     * @access  public
     * @param   string method request method, e.g. HTTP_GET
     */
    function setMethod($method) {
      $this->method= $method;
    }

    /**
     * Set request parameters
     *
     * @access  public
     * @param   mixed p either a string, a PostData object or an associative array
     */
    function setParameters($p) {
      if (is_a($p, 'RequestData')) {
        $this->parameters= &$p;
      } elseif (is_string($p)) {
        parse_str($p, $this->parameters); 
      } else {
        $this->parameters= array_merge($this->url->getParams(), $p);
      }
    }
    
    /**
     * Set header
     *
     * @access  public
     * @param   string k header name
     * @param   string v header value
     */
    function setHeader($k, $v) {
      $this->headers[$k]= $v;
    }

    /**
     * Add headers
     *
     * @access  public
     * @param   array headers
     */
    function addHeaders($headers) {
      foreach ($headers as $key => $header) {
        $this->headers[is_a($header, 'Header') ? $header->getName() : $key] = $header;
      }
    }
    
    /**
     * Get request string
     *
     * @access  public
     * @return  string
     */
    function getRequestString() {
      if (is_a($this->parameters, 'RequestData')) {
        $query= "\0".$this->parameters->getData();
      } else {
        $query= '';
        if (is_array($this->parameters)) foreach ($this->parameters as $k => $v) {
          $query.= '&'.$k.'='.urlencode($v);
        }
      }
      $target= $this->url->getPath('/');
      
      // Which HTTP method? GET and HEAD use query string, POST etc. use
      // body for passing parameters
      switch ($this->method) {
        case HTTP_HEAD:
        case HTTP_GET:
          $target.= empty($query) ? '' : '?'.substr($query, 1);
          $body= '';
          break;
          
        case HTTP_POST:
        default:
          $body= substr($query, 1);
          if (NULL !== $this->url->getQuery()) $target.= '?'.$this->url->getQuery();
          $this->headers['Content-Length']= strlen($body);
          if (empty($this->headers['Content-Type'])) {
            $this->headers['Content-Type']= 'application/x-www-form-urlencoded';
          }
          break;
      }
      
      $request= sprintf(
        "%s %s HTTP/%s\r\nHost: %s:%d\r\n",
        $this->method,
        $target,
        $this->version,
        $this->url->getHost(),
        $this->url->getPort(80)
      );
      
      // Add request headers
      foreach ($this->headers as $k => $v) {
        $request.= (is_a($v, 'Header') 
          ? $v->toString() 
          : $k.': '.$v
        )."\r\n";
      }
      
      return $request."\r\n".$body;
    }
    
    /**
     * Send request
     *
     * @access  public
     * @return  &peer.http.HttpResponse response object
     */
    function &send($timeout= 60) {
      $s= &new Socket($this->url->getHost(), $this->url->getPort(80));
      $s->setTimeout($timeout);
      
      $request= $this->getRequestString();
      try(); {
        $s->connect() && $s->write($request);
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      
      return new HttpResponse($s);
    }
  }
?>
