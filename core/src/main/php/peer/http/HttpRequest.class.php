<?php
/* This class is part of the XP framework
 * 
 * $Id: HttpRequest.class.php 14881 2010-10-01 07:46:08Z friebe $
 */

  uses(
    'lang.IllegalStateException',
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
      $headers    = array('Connection' => array('close')),
      $parameters = array(),
      $content    = NULL,
      $contentSet = FALSE;

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
        $this->headers['Authorization']= array('Basic '.base64_encode($url->getUser().':'.$url->getPassword()));
      }
      $port= $this->url->getPort(-1);
      $this->headers['Host']= array($this->url->getHost().(-1 == $port ? '' : ':'.$port));
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
     * @param   var p either a string, a RequestData object or an associative array
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
     * Set a single request parameter
     *
     * @param   string name
     * @param   string value
     */
    public function setParameter($name, $value) {
      $this->parameters[$name]= $value;
    }
    
    /**
     * Specifically set a content.
     * All given parameters will then be added to the url instead
     *
     * @param   string  content
     */
    public function setContent($content) {
      $this->content= $content;
      $this->contentSet= TRUE;
    }

    /**
     * Clears any set content
     */
    public function clearContent() {
      $this->content= NULL;
      $this->contentSet= FALSE;
    }

    /**
     * Set header
     *
     * @param   string k header name
     * @param   var v header value either a string, string[] or peer.Header
     */
    public function setHeader($k, $v) {
      if (is_array($v)) {
        $this->headers[$k]= $v;
      } else {
        $this->headers[$k]= array($v);
      }
    }

    /**
     * Add headers
     *
     * @param   [:var] headers
     */
    public function addHeaders($headers) {
      foreach ($headers as $key => $header) {
        $this->setHeader($header instanceof Header ? $header->getName() : $key, $header);
      }
    }

    /**
     * Will return the set parameters or content url encoded
     * Only handles array values with depth 1. Deeper levels are not supported.
     *
     * @return  string
     */
    protected function getEncodedParameters() {
      if ($this->parameters instanceof RequestData) {
        $this->addHeaders($this->parameters->getHeaders());
        $sEncoded= $this->parameters->getData();
      } else {
        $aEncoded= array();
        foreach ($this->parameters as $name => $value) {
          if (is_array($value)) {
            foreach ($value as $k => $v) {
              $aEncoded[]= $name.'['.$k.']='.urlencode($v);
            }
          } else {
            $aEncoded[]= $name.'='.urlencode($value);
          }
        }
        $sEncoded= implode('&', $aEncoded);
      }
      return $sEncoded;
    }

    /**
     * Will return the encoded content
     *
     * @return  string  
     */
    protected function getEncodedContent() {
      if ($this->content instanceof RequestData) {
        $this->addHeaders($this->content->getHeaders());
        return $this->content->getData();
      } else if (is_array($this->content)) {
        $aEncoded= array();
        foreach ($this->content as $name => $value) {
          if (is_array($value)) {
            foreach ($value as $k => $v) {
              $aEncoded[]= $name.'['.$k.']='.urlencode($v);
            }
          } else {
            $aEncoded[]= $name.'='.urlencode($value);
          }
        }
        return implode('&', $aEncoded);
      } else {
        return $this->content;
      }
    }

    /**
     * Returns payload.
     *
     * Old behaviour (no body set):
     *  Params will be either placed in body or URI,
     *  depending on the method
     *
     * New behaviour (a body was set):
     *  Params will go to the URI
     *  Body will go to the body
     *
     * @param   bool    withBody
     * @return  string  payload
     * @throws  lang.InvalidStateException  if body was set with a none supported method
     */
    protected function getPayload($withBody) {
      $content= NULL;
      $addParamsToURI= TRUE;
      $paramsEncoded= $this->getEncodedParameters();

      if (TRUE === $this->contentSet) {
        // trigger with-"setBody" behaviour
        switch ($this->method) {
          case HttpConstants::GET:    // might not be forbidden in RFC. if this is the case move to below
          case HttpConstants::HEAD:
          case HttpConstants::OPTIONS:
            throw new IllegalStateException('A '.$this->method.' request does not allow a body');
            break;

          default:
            $content= $this->getEncodedContent();
            break;
        }
      } else {
        // trigger pre-"setBody" behaviour
        // params will either go to the uri or the content
        switch ($this->method) {
          case HttpConstants::HEAD:
          case HttpConstants::GET:
          case HttpConstants::DELETE:
          case HttpConstants::OPTIONS:
            // intentionally nothing
            break;

          default:
            $addParamsToURI= FALSE;
            $content= $paramsEncoded;
            break;
        }
      }

      $queryString= array();
      if (NULL !== $this->url->getQuery()) {
        $queryString[]= $this->url->getQuery();
      }
      if ((TRUE === $addParamsToURI) &&
          (!empty($paramsEncoded))
      ) {
        $queryString[]= $paramsEncoded;
      }

      $target= $this->target;
      $target.= (count($queryString) > 0) ? '?'.implode('&', $queryString) : '';

      if (!is_null($content)) {
        $this->headers['Content-Length']= array(max(0, strlen($content)));
        if (empty($this->headers['Content-Type'])) {
          $this->headers['Content-Type']= array('application/x-www-form-urlencoded');
        }
      }

      $request= sprintf(
        "%s %s HTTP/%s\r\n",
        $this->method,
        $target,
        $this->version
      );

      // Add request headers
      foreach ($this->headers as $k => $v) {
        foreach ($v as $value) {
          $request.= ($value instanceof Header ? $value->toString() : $k.': '.$value)."\r\n";
        }
      }

      if (!$withBody) {
        $content= '';
      }
      return $request."\r\n".$content;
    }

    /**
     * Returns HTTP request headers as being written to server
     *
     * @return  string
     */
    public function getHeaderString() {
      return $this->getPayload(FALSE);
    }
    
    /**
     * Get request string
     *
     * @return  string
     */
    public function getRequestString() {
      return $this->getPayload(TRUE);
    }
  }
?>
