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
    var
      $env= array();
      
    /**
     * Helper method to encode a string
     *
     * @access  private
     * @param   string str string to be encoded
     * @return  string
     */
    function _encode($str) {
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
    function setEnvironment($env) {
      $this->env= $env;
    }

    /**
     * Get request string
     *
     * @access  public
     * @return  string
     */
    function getRequestString() {
      $path= $this->url->getPath('/');
      $p= strrpos($path, '/');
      $servlet= substr($path, $p+ 1);
      $zone= substr($path, 0, $p);
      
      // Protocol version
      switch ($this->url->getScheme()) {
          
        case 'ajpv13':
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
            $this->_encode($this->url->getQuery()).
            $this->_encode($this->env['REMOTE_ADDR']).
            $this->_encode($this->env['REMOTE_HOST']).
            $this->_encode($this->env['REMOTE_USER']).
            $this->_encode($this->env['AUTH_TYPE']).
            $this->_encode($this->url->getPort(80)).
            $this->_encode($this->method).
            $this->_encode($this->url->getURL()).
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
            $p.= "\x03".(is_a($v, 'Header') 
              ? $this->_encode($v->getName()).$this->_encode($v->getValue())
              : $this->_encode($k).$this->_encode($v)
            );
          }
          $p.= "\x04";
          
          // Request entity
          if (is_a($this->parameters, 'RequestData')) {
            $query= "\x00".$this->parameters->getData();
          } else {
            $query= '';
            foreach ($this->parameters as $k => $v) {
              $query.= '&'.$k.'='.urlencode($v);
            }
          }
          $p.= substr($query, 1);
          break;
      }
      
      return $p;
    }  
  }
?>
