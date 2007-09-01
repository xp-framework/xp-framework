<?php
/* This class is part of the XP framework
 *
 * $Id: AjpRequest.class.php 8975 2006-12-27 18:06:40Z friebe $ 
 */

  namespace org::apache::ajp;

  ::uses('peer.http.HttpRequest');

  /**
   * AJP Request
   *
   * @see      xp://org.apache.ajp.AjpConnection
   * @purpose  Java servlet integration
   */
  class AjpRequest extends peer::http::HttpRequest {
    public
      $env= array();
      
    /**
     * Helper method to encode a string
     *
     * @param   string str string to be encoded
     * @return  string
     */
    protected function _encode($str) {
      if (NULL === $str) return '\xff\xff';
      $l= strlen($str);
      return chr(($l >> 8) & 0xFF).chr($l & 0xff).$str;
    }
  
    /**
     * Set environment.
     *
     * @param   array env
     */
    public function setEnvironment($env) {
      $this->env= $env;
    }

    /**
     * Get request string
     *
     * @return  string
     */
    public function getRequestString() {
      static $methods= array(
        'OPTIONS'              => 0x0001,
        'GET'                  => 0x0002,
        'HEAD'                 => 0x0003,
        'POST'                 => 0x0004,
        'PUT'                  => 0x0005,
        'DELETE'               => 0x0006,
        'TRACE'                => 0x0007,
        'PROPFIND'             => 0x0008,
        'PROPPATCH'            => 0x0009,
        'MKCOL'                => 0x000A,
        'COPY'                 => 0x000B,
        'MOVE'                 => 0x000C,
        'LOCK'                 => 0x000D,
        'UNLOCK'               => 0x000E,
        'ACL'                  => 0x000F
      );
      static $headermap= array(
        'accept'               => 0xA001,
        'accept-charset'       => 0xA002,
        'accept-encoding'      => 0xA003,
        'accept-language'      => 0xA004,
        'authorization'        => 0xA005,
        'connection'           => 0xA006,
        'content-type'         => 0xA007,
        'content-length'       => 0xA008,
        'cookie'               => 0xA009,    
        'cookie2'              => 0xA00A,
        'host'                 => 0xA00B,
        'pragma'               => 0xA00C,
        'referer'              => 0xA00D,
        'user-agent'           => 0xA00E
      );
        
      $path= $this->url->getPath('/');
      $p= strrpos($path, '/');
      $servlet= substr($path, $p+ 1);
      $zone= substr($path, 0, $p);

      if (is('RequestData', $this->parameters)) {
        $query= "\x00".$this->parameters->getData();
      } else {
        $query= '';
        foreach ($this->parameters as $k => $v) {
          $query.= '&'.$k.'='.urlencode($v);
        }
      }
      $query= substr($query, 1);
      $length= strlen($query);
      
      // Protocol version
      switch ($this->url->getScheme()) {
          
        case 'ajpv13':
          $p= "\x02".(
            $methods[$this->method].
            $this->_encode($this->protocol).
            $this->_encode($this->env['REQUEST_URI']).
            $this->_encode($this->env['REMOTE_ADDR']).
            $this->_encode($this->env['REMOTE_HOST']).
            $this->_encode($this->env['SERVER_NAME']).
            $this->env['SERVER_PORT'].
            (empty($this->env['HTTPS']) ? 0 : 1).
            sizeof($this->headers)
          );
          
          // Note: The content-length header is extremely important. If it is 
          // present and non-zero, the container assumes that the request has 
          // a body (a POST request, for example), and immediately reads a 
          // separate packet off the input stream to get that body.
          if (in_array($this->method, array(HTTP_GET, HTTP_POST))) {
            $this->headers['Content-Length']= $length;
            if (empty($this->headers['Content-Type'])) {
              $this->headers['Content-Type']= 'application/x-www-form-urlencoded';
            }
          } else {
            $this->headers['Content-Length']= 0;
          }
          
          // Headers
          foreach ($this->headers as $k => $v) {
            $name= is('Header', $v) ? $v->getName() : $k;
            $idx= strtolower($name);
            $p.= isset($headermap[$idx]) ? $headermap[$idx] : $this->_encode($name);
            $p.= $this->_encode(is('Header', $v) ? $v->getValueRepresentation() : $v);
          }
          $p.= "\xFF";
          
          // TBD: Optional data
          
          // Request entity
          $p.= $query;
          
          break;

        case 'ajpv12':
        default:
          $p= "\x01".(
            $this->_encode($this->method).
            $this->_encode($zone).
            $this->_encode($servlet).
            $this->_encode($this->env['SERVER_NAME']).
            $this->_encode($this->env['DOCUMENT_ROOT']).
            $this->_encode($this->env['PATH_INFO']).
            $this->_encode($this->env['PATH_TRANSLATED']).
            $this->_encode($this->env['QUERY_STRING']).
            $this->_encode($this->env['REMOTE_ADDR']).
            $this->_encode($this->env['REMOTE_HOST']).
            $this->_encode($this->env['REMOTE_USER']).
            $this->_encode($this->env['AUTH_TYPE']).
            $this->_encode($this->env['SERVER_PORT']).
            $this->_encode($this->method).
            $this->_encode($this->env['REQUEST_URI']).
            $this->_encode(NULL).
            $this->_encode($this->env['SCRIPT_NAME']).
            $this->_encode($this->env['SERVER_NAME']).
            $this->_encode($this->env['SERVER_PORT']).
            $this->_encode($this->env['SERVER_PROTOCOL']).
            $this->_encode($this->env['SERVER_SIGNATURE']).
            $this->_encode($this->env['SERVER_SOFTWARE']).
            $this->_encode(NULL).                          // JServ Route
            $this->_encode('').                            // v1.2 ompatibility
            $this->_encode('')                             // v1.2 compatibility
          );
          
          // Request attributes (AJP_ENV_VARS)
          foreach ($this->attributes as $k => $v) {
            $p.= "\x05".$this->_encode($k).$this->_encode($v);
          }
          
          // Headers
          foreach ($this->headers as $k => $v) {
            $p.= "\x03".(is('Header', $v) 
              ? $this->_encode($v->getName()).$this->_encode($v->getValue())
              : $this->_encode($k).$this->_encode($v)
            );
          }
          $p.= "\x04";
          
          // Request entity
          $p.= $query;
          break;
      }
      
      return $p;
    }  
  }
?>
