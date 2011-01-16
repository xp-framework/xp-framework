<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Defines the response sent by the server to the client
   *
   * @purpose  Interface
   */  
  interface Response {

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
    public function setCookie($cookie);

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
    public function setHeader($name, $value);

    /**
     * Sets status code
     *
     * @param   int sc statuscode
     * @see     rfc://2616#10
     */
    public function setStatus($sc);

    /**
     * Sets the length of the content body in the response. 
     *
     * @param   int length
     */
    public function setContentLength($length);

    /**
     * Sets the content type of the response being sent to the client.
     *
     * @param   string type
     */
    public function setContentType($type);

    /**
     * Returns whether the response has been comitted yet.
     *
     * @return  bool
     */
    public function isCommitted();

    /**
     * Gets the output stream
     *
     * @param   io.streams.OutputStream
     */
    public function getOutputStream();

    /**
     * Flushes this response, that is, writes all headers to the outputstream
     *
     */
    public function flush();
    
  }
?>
