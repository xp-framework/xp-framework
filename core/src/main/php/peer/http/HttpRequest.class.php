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
    'peer.http.AbstractHttpRequestData',
    'peer.http.HttpResponse',
    'peer.http.HttpRequestData',
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

    const
      MIME_TYPE_DEFAULT                   = 'text/plain';

    public
      $url            = NULL,
      $method         = HttpConstants::GET,
      $target         = '',
      $version        = HttpConstants::VERSION_1_1,
      $headers        = array('Connection' => array('close')),
      $parameters     = array(),
      $body           = NULL,
      $contentHeaders = NULL;

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
     * Set request parameters.
     * Be aware, that setting a RequestData (e.g. FormRequestData) as param, may break the URL!
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
     * Specifically set a body.
     * If no peer.http.AbstractHttpRequestData is given, $body will be transformed to peer.http.HttpRequestData.
     * If this method is used, all eventually set parameters will later be added to the url,
     * which may lead to errors if RequestData was given!
     *
     * @param   var  body anything except none serializable objects (exception peer.http.RequestData)
     */
    public function setBody($body) {
      if(!$body instanceof AbstractHttpRequestData) {
        // create one from the given data
        if($body instanceof RequestData) {
          $data= $body->getData();
          $headers= $body->getHeaders();
          $body= create(new HttpRequestData($data))
            ->withHeaders($headers);
        } else {
          $body= new HttpRequestData($body);
        }
      }
      $this->body= $body;
    }

    /**
     * Clears any set body
     */
    public function clearBody() {
      $this->body= NULL;
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
     * Will return if a header with the given name was set
     *
     * @param string name
     * @return bool
     */
    public function hasHeader($name) {
      return (array_key_exists($name, $this->headers));
    }

    /**
     * Will return if a body is set
     *
     * @return bool
     */
    protected function isBodySet() {
      return (NULL !== $this->body);
    }
    
    /**
     * Will return all headers from this request and the set content
     * Headers set in the request have a higher priority, 
     * so in case of conflict those are used.
     *
     * @return [:string] peer.Header|array
     */
    protected function getHeaders() {
      $headers= $this->headers;
      if($this->isBodySet()) {
        foreach($this->body->getHeaders() as $header) {
          $headerName= $header->getName();
          if(!isset($headers[$headerName])) {
            $headers[$headerName]= array($header);
          }
        }
      }
      return $headers;
    }
    
    /**
     * Will return the set parameters or content url encoded
     * Handles array values with unrestricted depth.
     *
     * @param   mixed   data
     * @param   mixed   prefix
     * @return  string  encodeData
     */
    protected function encodeData($data, $prefix= '') {
      $prefix= trim($prefix);
      if ($data instanceof RequestData) {
        return $prefix.$data->getData();
      } else if (is_array($data)) {
        $aEncoded= array();
        foreach ($data as $name => $value) {
          if ('' !== $prefix) {
            $name= $prefix.'['.$name.']';
          }
          if (is_array($value)) {
            $aEncoded[]= $this->encodeData($value, $name);
          } else {
            $aEncoded[]= $name.'='.urlencode($value);
          }
        }
        return implode('&', $aEncoded);
      } else {
        return $prefix.$data;
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
     * @throws  lang.IllegalStateException  if body was set with a none supported method
     */
    protected function getPayload($withBody) {
      $body= NULL;
      $paramsEncoded= '';

      if ($this->isBodySet()) {
        // Trigger with-"setBody" behaviour
        switch ($this->method) {
          case HttpConstants::GET:    // might not be forbidden in RFC. if this is the case move to default
          case HttpConstants::HEAD:
          case HttpConstants::OPTIONS:
            throw new IllegalStateException('A '.$this->method.' request does not allow a body');
            break;

          default:
            // TBD: Prevent params if RequestData was given?
            $paramsEncoded= $this->encodeData($this->parameters);
            $body= $this->encodeData($this->body);
            break;
        }
      } else {
        // Trigger pre-"setBody" behaviour
        // Params will either go to the uri or the body
        $encodedData= $this->encodeData($this->parameters);
        if($this->parameters instanceof RequestData) {
          // Headers are always set regardless where the params will go
          // Actually wrong behavior... but keep as is
          $this->addHeaders($this->parameters->getHeaders());
        }
        switch ($this->method) {
          case HttpConstants::HEAD:
          case HttpConstants::GET:
          case HttpConstants::DELETE:
          case HttpConstants::OPTIONS:
            $paramsEncoded= $encodedData;
            break;

          default:
            $body= $encodedData;
            break;
        }

        if (NULL !== $body) {
          $this->headers['Content-Length']= array(max(0, strlen($body)));
          if (empty($this->headers['Content-Type'])) {
            $this->headers['Content-Type']= array('application/x-www-form-urlencoded');
          }
        }
      }

      $queryString= array();
      if (NULL !== $this->url->getQuery()) {
        $queryString[]= $this->url->getQuery();
      }
      if (!empty($paramsEncoded)) {
        $queryString[]= $paramsEncoded;
      }

      $target= $this->target;
      $target.= (sizeOf($queryString) > 0) ? '?'.implode('&', $queryString) : '';

      // Build request
      $request= sprintf(
        '%s %s HTTP/%s'.HttpConstants::CRLF,
        $this->method,
        $target,
        $this->version
      );

      foreach ($this->getHeaders() as $k => $v) {
        foreach ($v as $value) {
          $request.= ($value instanceof Header ? $value->toString() : $k.': '.$value).HttpConstants::CRLF;
        }
      }
      $request.= HttpConstants::CRLF;

      if ($withBody) {
        $request.= $body;
      }
      return $request;
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
