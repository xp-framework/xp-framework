<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */

  /**
   * HTTP response
   *
   * @see      xp://peer.http.HttpConnection
   * @purpose  Represents a HTTP response
   */
  class HttpResponse extends Object {
    var
      $statuscode    = 0,
      $message       = '',
      $version       = '',
      $headers       = array();
    
    var
      $_headerlookup = array();
      
    /**
     * Constructor
     *
     * @access  public
     * @param   &lang.Object stream
     */
    function __construct(&$stream) {
      $this->stream= &$stream;
      parent::__construct();
    }

    /**
     * Read status line
     *
     * @access  private
     * @return  bool success
     */    
    function _readstatus() {
      $s= chop($this->stream->read());
      if (3 != ($r= sscanf(
        $s, 
        'HTTP/%d.%d %3d', 
        $major, 
        $minor, 
        $this->statuscode
      ))) {
        return throw(new FormatException('"'.$s.'" is not a valid HTTP response ['.$r.']'));
      }
      
      $this->message= substr($s, 12);
      $this->version= $major.'.'.$minor;
      
      return TRUE;
    }
    
    /**
     * Read head if necessary
     *
     * @access  protected
     * @return  bool success
     */
    function _readhead() {
      if (0 != $this->statuscode) return TRUE;
      if (!$this->_readstatus()) return FALSE;
      
      // HTTP/1.x 100 Continue
      if (100 == $this->statuscode) {
        while (!$this->stream->eof()) {
          if ('' == chop($this->stream->read())) break;
        }
        
        if (!$this->_readstatus()) return FALSE;
      }
      
      // Read rest of headers
      while (!$this->stream->eof()) {
        $l= chop($this->stream->read());
        if ('' == $l) break;
        
        list($k, $v)= explode(': ', $l, 2);
        $this->headers[$k]= $v;
      }
      
      return TRUE;
    }
    
    /**
     * Read data
     *
     * @access  public
     * @param   int size default 8192
     * @param   bool binary default FALSE
     * @return  string buf or FALSE to indicate EOF
     */
    function readData($size= 8192, $binary= FALSE) {
      static $chunked;
      
      if (!$this->_readhead()) return FALSE;        // Read head if not done before
      if ($this->stream->eof()) {                   // EOF, return FALSE to indicate end
        $this->stream->close();
        $this->stream->__destruct();
        unset($this->stream);
        return FALSE;
      }
      if (!isset($chunked)) {                       // Check for "chunked"
        $chunked= stristr($this->getHeader('Transfer-Encoding'), 'chunked');
      }
      
      $func= $binary ? 'readBinary' : 'read';
      if (FALSE === ($buf= $this->stream->$func($size))) return FALSE;
      
      // Handle chunked
      if (
        $chunked &&
        !$binary && 
        preg_match('/^([0-9a-fA-F]+)(( ;.*)| )?\r\n$/', $buf, $regs)
      ) {
        return $this->readData($size, $binary);
      }
      
      return $buf;
    }
    
    /**
     * Return nice string representation
     *
     * Example:
     * <pre>
     * peer.http.HttpResponse {
     *   HTTP/1.1 300  Multiple Choices
     *   [Date                ] Sat, 01 Feb 2003 01:27:26 GMT
     *   [Server              ] Apache/1.3.27 (Unix)
     *   [Connection          ] close
     *   [Transfer-Encoding   ] chunked
     *   [Content-Type        ] text/html; charset=iso-8859-1
     * }
     * </pre>
     *
     * @access  public
     * @return  toString
     */
    function toString() {
      if (!$this->_readhead()) return parent::toString();
      
      $h= '';
      foreach ($this->headers as $k => $v) {
        $h.= sprintf("  [%-20s] %s\n", $k, $v);
      }
      return sprintf(
        "%s {\n  HTTP/%s %3d %s\n%s}",
        $this->getClassName(),
        $this->version,
        $this->statuscode,
        $this->message,
        $h
      );
    }

    /**
     * Get HTTP statuscode
     *
     * @access  public
     * @return  int status code
     */
    function getStatusCode() {
      return $this->_readhead() ? $this->statuscode : FALSE;
    }
    
    /**
     * Get response headers as an associative array
     *
     * @access  public
     * @return  array headers
     */
    function getHeaders() {
      return $this->_readhead() ? $this->headers : FALSE;
    }

    /**
     * Get response header by name
     * Note: The lookup is performed case-insensitive
     *
     * @access  public
     * @return  string value or NULL if this header does not exist
     */
    function getHeader($name) {
      if (!$this->_readhead()) return FALSE;
      if (empty($this->_headerlookup)) {
        $this->_headerlookup= array_change_key_case($this->headers, CASE_LOWER);
      }
      $name= strtolower($name);
      return isset($this->_headerlookup[$name]) ? $this->_headerlookup[$name] : NULL;
    }
  
  }
?>
