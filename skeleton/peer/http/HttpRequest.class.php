<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */

  define('HTTP_GET',     'GET');
  define('HTTP_POST',    'POST');
  define('HTTP_HEAD',    'HEAD');
  
  uses(
    'peer.Socket',
    'io.IOException',
    'peer.URL'
   );
  
  /**
   * HTTP request
   *
   * @see      rfc://2616
   * @purpose  Wrap
   */
  class HttpRequest extends Object {
    var
      $url      = NULL,
      $method   = HTTP_GET,
      $params   = array(),
      $version  = '1.1',
      $headers  = array(
        'Connection' => 'close'
      );
      
    /**
     * Constructor
     *
     * @access  public
     * @param   &mixed url a string or a peer.URL object
     */
    function __construct(&$url) {
      if (is_string($url)) $this->url= &new URL($url); else $this->url= &$url;
      parent::__construct();
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
     * @param   mixed p either a string or an associative array
     */
    function setParameters($p) {
      if (is_string($p)) parse_str($p, $p); 
      $this->parameters= array_merge($this->url->getParams(), $p);
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
      $this->headers= array_merge($this->headers, $headers);
    }
    
    /**
     * Send request
     *
     * @access  public
     * @return  &peer.Socket stream to read response from
     */
    function &send() {
      $query= '';
      foreach ($this->parameters as $k => $v) {
        $query.= '&'.$k.'='.urlencode($v);
      }
      $target= $this->url->getPath();
      
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
          $this->headers['Content-Length']= strlen($body);
          $this->headers['Content-Type']= 'application/x-www-urlencoded';
          break;
      }
      
      $request= sprintf(
        "%s %s HTTP/%s\r\nHost: %s\r\n",
        $this->method,
        $target,
        $this->version,
        $this->url->getHost()
      );
      
      // Add request headers
      foreach ($this->headers as $k => $v) {
        $request.= (is_a($v, 'Header') 
          ? $v->toString() 
          : $k.': '.$v
        )."\r\n";
      }
      
      $s= &new Socket($this->url->getHost(), $this->url->getPort(80));
      try(); {
        $s->connect();
        $s->write($request."\r\n".$body);
      } if (catch('Exception', $e)) {
        throw($e);
      }
      
      return $s;
    }
  }
?>
