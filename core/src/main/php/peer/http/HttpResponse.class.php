<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */

  uses('io.streams.InputStream', 'peer.http.HttpInputStream');

  /**
   * HTTP response
   *
   * @see     xp://peer.http.HttpConnection
   * @test    xp://net.xp_framework.unittest.peer.http.HttpResponseTest
   * @test    xp://net.xp_framework.unittest.peer.http.HttpInputStreamTest
   */
  class HttpResponse extends Object {
    public
      $statuscode    = 0,
      $message       = '',
      $version       = '',
      $headers       = array(),
      $chunked       = NULL;
    
    protected
      $stream        = NULL,
      $buffer        = '',
      $_headerlookup = array();
      
    /**
     * Constructor
     *
     * @param   io.streams.InputStream stream
     */
    public function __construct(InputStream $stream) {
      $this->stream= $stream;
      
      // Read status line and headers
      do { $this->readHeader(); } while (100 === $this->statuscode);

      // Check for chunked transfer encoding
      $this->chunked= (bool)stristr($this->getHeader('Transfer-Encoding'), 'chunked');
    }
    
    /**
     * Scan stream until we we find a certain character
     *
     * @param   string char
     * @return  string
     */
    protected function scanUntil($char) {
      $pos= strpos($this->buffer, $char);
      
      // Found no line ending in buffer, read until we do!
      while (FALSE === $pos) {
        if ($this->stream->available() <= 0) {
          $pos= strlen($this->buffer);
          break;
        }
        $this->buffer.= $this->stream->read();
        $pos= strpos($this->buffer, $char);
      }

      // Return line, remove from buffer
      $line= substr($this->buffer, 0, $pos);
      $this->buffer= substr($this->buffer, $pos+ 1);
      return $line;
    }

    /**
     * Read a chunk
     *
     * @param   int bytes
     * @return  string
     */
    protected function readChunk($bytes) {
      $len= strlen($this->buffer);
      
      // Not enough data, read until it's here!
      while ($len < $bytes) {
        if ($this->stream->available() <= 0) break;
        $this->buffer.= $this->stream->read();
        $len= strlen($this->buffer);
      }
      
      // Return chunk, remove from buffer
      $chunk= substr($this->buffer, 0, $bytes);
      $this->buffer= substr($this->buffer, $bytes);
      return $chunk;
    }
    
    /**
     * Reads the header (status line and key/value pairs).
     *
     * @throws  lang.FormatException
     */
    protected function readHeader() {
    
      // Status line
      $status= $this->scanUntil("\n");
      $r= sscanf($status, "HTTP/%[0-9.] %3d %[^\r]", $this->version, $this->statuscode, $this->message);
      if ($r < 2) {
        throw new FormatException('"'.$status.'" is not a valid HTTP response ['.$r.']');
      }

      // Headers
      while ($line= $this->scanUntil("\n")) {
        if (2 != sscanf($line, "%[^:]: %[^\r\n]", $k, $v)) break;
        
        $l= strtolower($k);
        if (!isset($this->_headerlookup[$l])) {
          $this->_headerlookup[$l]= $k;
        } else {
          $k= $this->_headerlookup[$l];
        }
        if (!isset($this->headers[$k])) {
          $this->headers[$k]= array($v);
        } else {
          $this->headers[$k][]= $v;
        }
      }
    }

    /**
     * Read data
     *
     * @param   int size default 8192 maximum size to read
     * @return  string buf or FALSE to indicate EOF
     */
    public function readData($size= 8192) {
      if (!$this->chunked) {
        if (!($buf= $this->readChunk($size))) {
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
      if (!($indicator= $this->scanUntil("\n"))) return $this->closeStream();
      if (!(sscanf($indicator, "%x%s\r", $chunksize, $extension))) {
        $this->closeStream();
        throw new IOException(sprintf(
          'Chunked transfer encoding: Indicator line "%s" invalid', 
          addcslashes($indicator, "\0..\17")
        ));
      }

      // A chunk of size 0 means we're at the end of the document. We 
      // ignore any trailers.
      if (0 == $chunksize) return $this->closeStream();

      // A chunk is terminated by \r\n, so scan over two more characters
      $chunk= $this->readChunk($chunksize);
      $this->readChunk(2);
      return $chunk;
    }
    
    /**
     * Closes the stream and returns FALSE
     *
     * @return  bool
     */
    public function closeStream() {
      $this->stream->close();
      return FALSE;
    }

    /**
     * Returns an input stream to read data
     *
     * @return  io.streams.InputStream
     */
    public function getInputStream() {
      return new HttpInputStream($this);
    }

    /**
     * Returns HTTP response headers as read from server
     *
     * @return  string
     */
    public function getHeaderString() {
      $s= 'HTTP/'.$this->version.' '.$this->statuscode.' '.$this->message."\r\n";
      foreach ($this->headers as $k => $v) {
        $s.= $k.': '.implode(', ', $v)."\r\n";
      }
      return $s."\r\n";
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
      $h= '';
      foreach ($this->headers as $k => $v) {
        $h.= sprintf("  [%-20s] { %s }\n", $k, implode(', ', $v));
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
     * @deprecated Use statusCode() instead
     * @return  int status code
     */
    public function getStatusCode() {
      return $this->statuscode;
    }

    /**
     * Get HTTP statuscode
     *
     * @return  int status code
     */
    public function statusCode() {
      return $this->statuscode;
    }

    /**
     * Get HTTP message
     *
     * @deprecated Use message() instead
     * @return  string
     */
    public function getMessage() {
      return $this->message;
    }

    /**
     * Get HTTP message
     *
     * @return  string
     */
    public function message() {
      return $this->message;
    }
    
    /**
     * Get response headers as an associative array
     *
     * @deprecated Use headers() instead
     * @return  [:string] headers
     */
    public function getHeaders() {
      $headers= array();
      foreach ($this->headers as $name => $values) {
        $headers[$name]= end($values);
      }
      return $headers;
    }

    /**
     * Get response headers as an associative array
     *
     * @return  [:string[]] headers
     */
    public function headers() {
      return $this->headers;
    }

    /**
     * Get response header by name
     * Note: The lookup is performed case-insensitive
     *
     * @deprecated Use header() instead
     * @return  string value or NULL if this header does not exist
     */
    public function getHeader($name) {
      $l= strtolower($name);
      return isset($this->_headerlookup[$l]) ? end($this->headers[$this->_headerlookup[$l]]) : NULL;
    }

    /**
     * Get response header by name
     * Note: The lookup is performed case-insensitive
     *
     * @return  string[] value or NULL if this header does not exist
     */
    public function header($name) {
      $l= strtolower($name);
      return isset($this->_headerlookup[$l]) ? $this->headers[$this->_headerlookup[$l]] : NULL;
    }
  }
?>
