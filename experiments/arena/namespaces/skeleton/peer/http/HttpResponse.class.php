<?php
/* This class is part of the XP framework
 * 
 * $Id: HttpResponse.class.php 10594 2007-06-11 10:04:54Z friebe $
 */

  namespace peer::http;

  ::uses('peer.SocketException');

  /**
   * HTTP response
   *
   * @see      xp://peer.http.HttpConnection
   * @purpose  Represents a HTTP response
   */
  class HttpResponse extends lang::Object {
    public
      $statuscode    = 0,
      $message       = '',
      $version       = '',
      $headers       = array(),
      $chunked       = NULL;
    
    public
      $_headerlookup = array();
      
    /**
     * Constructor
     *
     * @param   lang.Object stream
     */
    public function __construct($stream) {
      $this->stream= $stream;
      
    }

    /**
     * Read status line
     *
     * @return  bool success
     */    
    protected function _readstatus() {
      try {
        $str= $this->stream->read();
      } catch (peer::SocketException $e) {
        throw($e);
      }
      
      $s= chop($str);
      if (3 > ($r= sscanf(
        $s, 
        "HTTP/%d.%d %3d %[^\r]",
        $major, 
        $minor, 
        $this->statuscode,
        $this->message
      ))) {
        throw(new lang::FormatException('"'.$s.'" is not a valid HTTP response ['.$r.']'));
      }
      
      $this->version= $major.'.'.$minor;
      return TRUE;
    }
    
    /**
     * Read head if necessary
     *
     * @return  bool success
     */
    protected function _readhead() {
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

      // Check for chunked transfer encoding
      $this->chunked= (bool)stristr($this->getHeader('Transfer-Encoding'), 'chunked');
      
      return TRUE;
    }
    
    /**
     * Read data
     *
     * @param   int size default 8192
     * @param   bool binary default FALSE
     * @return  string buf or FALSE to indicate EOF
     */
    public function readData($size= 8192, $binary= FALSE) {
      if (!$this->_readhead()) return FALSE;        // Read head if not done before
      if ($this->stream->eof()) return $this->closeStream();
      
      if (!$this->chunked) {
        $func= $binary ? 'readBinary' : 'read';
        if (!($buf= $this->stream->$func($size))) {
          return $this->closeStream();
        }

        return $buf;
      }

      // Handle chunked transfer encoding. In chunked transfer encoding,
      // a hexadecimal number followed by optional text is on a line by
      // itself. The line is terminated by \r\n. The hexadecimal number
      // indicates the size of the chunk. The first chunk indicator comes 
      // immediately after the headers. Note: We assume that a chunked 
      // indicator line will never be longer than 1024 bytes. We ignore
      // any chunk extensions. We ignore the size and boolean parameters
      // to this method completely to ensure functionality. For more 
      // details, see RFC 2616, section 3.6.1
      if (!($buf= $this->stream->read(1024))) return $this->closeStream();
      if (!(sscanf($buf, "%x%s\r\n", $chunksize, $extension))) {
        throw(new io::IOException(sprintf(
          'Chunked transfer encoding: Indicator line "%s" invalid', 
          addcslashes($buf, "\0..\17")
        )));
        return $this->closeStream();
      }

      // A chunk of size 0 means we're at the end of the document. We 
      // ignore any trailers.
      if (0 == $chunksize) return $this->closeStream();

      // A chunk is terminated by \r\n, so add 2 to the chunksize. We will
      // trim these characters off later.
      $chunksize+= 2;

      // Read up until end of chunk
      $buf= '';
      do {
        if (!($data= $this->stream->readBinary($chunksize- strlen($buf)))) return $this->closeStream();
        $buf.= $data;
      } while (strlen($buf) < $chunksize);

      return rtrim($buf, "\r\n");
    }
    
    /**
     * Closes the stream if it's at EOF
     *
     * @return  boolean 
     */
    public function closeStream() {
      if ($this->stream->eof()) {
        $this->stream->close();
      }
      
      return FALSE;
    }
    
    /**
     * Return nice string representation
     *
     * Example:
     * <pre>
     *   peer.http.HttpResponse (HTTP/1.1 300 Multiple Choices) {
     *     [Date                ] Sat, 01 Feb 2003 01:27:26 GMT
     *     [Server              ] Apache/1.3.27 (Unix)
     *     [Connection          ] close
     *     [Transfer-Encoding   ] chunked
     *     [Content-Type        ] text/html; charset=iso-8859-1
     *   }
     * </pre>
     *
     * @return  toString
     */
    public function toString() {
      if (!$this->_readhead()) return parent::toString();
      
      $h= '';
      foreach ($this->headers as $k => $v) {
        $h.= sprintf("  [%-20s] %s\n", $k, $v);
      }
      return sprintf(
        "%s (HTTP/%s %3d %s) {\n%s}",
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
     * @return  int status code
     */
    public function getStatusCode() {
      return $this->_readhead() ? $this->statuscode : FALSE;
    }

    /**
     * Get HTTP message
     *
     * @return  string
     */
    public function getMessage() {
      return $this->message;
    }
    
    /**
     * Get response headers as an associative array
     *
     * @return  array headers
     */
    public function getHeaders() {
      return $this->_readhead() ? $this->headers : FALSE;
    }

    /**
     * Get response header by name
     * Note: The lookup is performed case-insensitive
     *
     * @return  string value or NULL if this header does not exist
     */
    public function getHeader($name) {
      if (!$this->_readhead()) return FALSE;
      if (empty($this->_headerlookup)) {
        $this->_headerlookup= array_change_key_case($this->headers, CASE_LOWER);
      }
      $name= strtolower($name);
      return isset($this->_headerlookup[$name]) ? $this->_headerlookup[$name] : NULL;
    }
  
  }
?>
