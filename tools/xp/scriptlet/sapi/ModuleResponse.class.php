<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('scriptlet.Response', 'util.cmd.Console');

  /**
   * PHP Module response.
   *
   * <ul>
   *   <li>The HTTP status line is written with header('HTTP/ [...]');</li>
   *   <li>Headers are written using header()</li>
   *   <li>Content is written using echo</li>
   * </ul>
   *
   * @see   php://php_sapi_name
   * @see   php://header
   */
  class CgiResponse extends Object implements Response {
    protected $out= NULL;
    protected $headers= array(
      'Content-Type' => 'text/html'
    );
    
    /**
     * Constructor
     *
     */
    public function __construct() {
      $this->out= new ScriptletOutputStream($this);
    }
    
    /**
     * Set a cookie. May be called multiple times with different cookies
     * to set more than one cookie.
     *
     * Example:
     * <code>
     *   $response->setCookie(new Cookie('lastvisit', date('Y-m-d')));
     * </code>
     *
     * @param   scriptlet.Cookie cookie
     */
    public function setCookie($cookie) {
      $this->headers['Set-Cookie']= $cookie->getHeaderValue();
    }

    /**
     * Adds a response header. If this header is already set, it will
     * be overwritten. Multiple headers *are* allowed but quite useless
     * for most applications.
     *
     * Example:
     * <code>
     *   $response->setHeader('X-Binford', '6100 (more power)');
     * </code>
     *
     * @param   string name header name
     * @param   string value header value
     */
    public function setHeader($name, $value) {
      $this->headers[$name]= $value;
    }

    /**
     * Sets status code
     *
     * @param   int sc statuscode
     * @see     rfc://2616#10
     */
    public function setStatus($sc) {
      $this->status= $sc;
    }

    /**
     * Sets the length of the content body in the response. 
     *
     * @param   int length
     */
    public function setContentLength($length) {
      $this->headers['Content-Length']= $length;
    }

    /**
     * Sets the content type of the response being sent to the client.
     *
     * @param   string type
     */
    public function setContentType($type) {
      $this->headers['Content-Type']= $length;
    }

    /**
     * Returns whether the response has been comitted yet.
     *
     * @return  bool
     */
    public function isCommitted() {
      return $this->out->isCommitted();
    }

    /**
     * Gets the output stream
     *
     * @return  scriptlet.ScriptletOutputStream
     */
    public function getOutputStream() {
      return $this->out;
    }
    
    /**
     * Flushes this response, that is, writes all headers to the outputstream
     *
     */
    public function flush() {
      $this->out->flush();
    }

    /**
     * Resets this response
     *
     */
    public function reset() {
      $this->out->reset();
    }
    
    /**
     * Writes headers directly to the output. Used by ScriptletOutputStream.
     *
     */
    public function writeHeaders() {
      header('HTTP/1.1 '.$this->status." No message");
      foreach ($this->headers as $name => $value) {
        header($name.': '.$value);
      }
    }

    /**
     * Writes bytes directly to the output. Used by ScriptletOutputStream.
     *
     * @param   string buffer
     */
    public function writeBytes($buffer) {
      echo $buffer;
    }
  }
?>
