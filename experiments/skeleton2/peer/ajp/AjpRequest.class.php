<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.http.HttpRequest');

  /**
   * AJP Request
   *
   * @see      xp://peer.ajp.AjpConnection
   * @purpose  Java servlet integration
   */
  class AjpRequest extends HttpRequest {
    public
      $env= array();
      
    /**
     * Helper method to encode a string
     *
     * @access  private
     * @param   string str string to be encoded
     * @return  string
     */
    private function _encode($str) {
      if (NULL === $str) return '\xff\xff';
      $l= strlen($str);
      return chr(($l >> 8) & 0xFF).chr($l & 0xff).$str;
    }
  
    /**
     * (Insert method's description here)
     *
     * @access  public
     * @param   
     * @return  
     */
    public function setEnvironment($env) {
      $this->env= $env;
    }

    /**
     * Get request string
     *
     * @access  public
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

      if (is_a($this->parameters, 'RequestData')) {
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
            self::_encode($this->protocol).
            self::_encode($this->env['REQUEST_URI']).
            self::_encode($this->env['REMOTE_ADDR']).
            self::_encode($this->env['REMOTE_HOST']).
            self::_encode($this->env['SERVER_NAME']).
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
            $name= is_a($v, 'Header') ? $v->getName() : $k;
            $idx= strtolower($name);
            $p.= isset($headermap[$idx]) ? $headermap[$idx] : self::_encode($name);
            $p.= self::_encode(is_a($v, 'Header') ? $v->getValueRepresentation() : $v);
          }
          $p.= "\xFF";
          
          // TBD: Optional data
          
          // Request entity
          $p.= $query;
          
          break;

        case 'ajpv12':
        default:
          $p= "\x01".(
            self::_encode($this->method).
            self::_encode($zone).
            self::_encode($servlet).
            self::_encode($this->env['SERVER_NAME']).
            self::_encode($this->env['DOCUMENT_ROOT']).
            self::_encode($this->env['PATH_INFO']).
            self::_encode($this->env['PATH_TRANSLATED']).
            self::_encode($this->env['QUERY_STRING']).
            self::_encode($this->env['REMOTE_ADDR']).
            self::_encode($this->env['REMOTE_HOST']).
            self::_encode($this->env['REMOTE_USER']).
            self::_encode($this->env['AUTH_TYPE']).
            self::_encode($this->env['SERVER_PORT']).
            self::_encode($this->method).
            self::_encode($this->env['REQUEST_URI']).
            self::_encode(NULL).
            self::_encode($this->env['SCRIPT_NAME']).
            self::_encode($this->env['SERVER_NAME']).
            self::_encode($this->env['SERVER_PORT']).
            self::_encode($this->env['SERVER_PROTOCOL']).
            self::_encode($this->env['SERVER_SIGNATURE']).
            self::_encode($this->env['SERVER_SOFTWARE']).
            self::_encode(NULL).                          // JServ Route
            self::_encode('').                            // v1.2 ompatibility
            self::_encode('')                             // v1.2 compatibility
          );
          
          // Request attributes (AJP_ENV_VARS)
          foreach ($this->attributes as $k => $v) {
            $p.= "\x05".self::_encode($k).self::_encode($v);
          }
          
          // Headers
          foreach ($this->headers as $k => $v) {
            $p.= "\x03".(is_a($v, 'Header') 
              ? self::_encode($v->getName()).self::_encode($v->getValue())
              : self::_encode($k).self::_encode($v)
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
