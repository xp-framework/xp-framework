<?php
/* This class is part of the XP framework
 *
 * $Id: HttpScriptletResponse.class.php 10594 2007-06-11 10:04:54Z friebe $
 */

  namespace scriptlet;
 
  uses('peer.http.HttpConstants');
 
  /**
   * Defines the response sent from the webserver to the client,
   * consisting from response headers and an optional body.
   *
   * An instance of this object is passed to the do* methods by
   * the <pre>process</pre> method.
   *
   * @see      xp://scriptlet.HttpScriptlet
   * @purpose  Provide a way to access the HTTP response
   */  
  class HttpScriptletResponse extends lang::Object {
    public
      $version=         '1.1',
      $content=         '',
      $statusCode=      HTTP_OK,
      $headers=         array();
    
    /**
     * Redirects the client to the specified location. Most HTTP clients
     * (such as all browsers) ignore the body if one is sent, search engines
     * _may_ not, and of course, your favorite command line tool (such as
     * telnet, socket, netcat) won't either.
     *
     * Therefore, it is generally a good idea to return FALSE from any of
     * the do* methods in your Scriptlet to indicate no farther processing
     * is needed and not send a body.
     *
     * @see     scriptlet.HttpScriptlet#doCreateSession
     * @param   string target an absolute URI
     */
    public function sendRedirect($location) {
      $this->statusCode= HTTP_FOUND;
      $this->setHeader('Location', $location);
    }
    
    /**
     * Sends a WWW-Authenticate header and sets the HTTP status code 401
     * (unauthorized). Uses Basic Authentication.
     *
     * @see     http://httpd.apache.org/docs/howto/auth.html
     * @see     rfc://2617
     * @param   string realm default 'Restricted area'
     */
    public function sendBasicAuthenticate($realm= 'Restricted area') {
      $this->statusCode= HTTP_AUTHORIZATION_REQUIRED;
      $this->setHeader('WWW-Authenticate', 'Basic realm="'.$realm.'"');
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
      $this->headers[]= $name.': '.$value;
    }

    /**
     * Sets the length of the content body in the response. 
     *
     * @param   int len
     */
    public function setContentLength($len) {
      $this->headers[]= 'Content-Length: '.$len;
    }

    /**
     * Sets the content type of the response being sent to the client, 
     * if the response has not been committed yet. The given content 
     * type may include a character encoding specification, for example, 
     * text/html; charset=UTF-8.
     *
     * @param   string type
     */
    public function setContentType($type) {
      $this->headers[]= 'Content-Type: '.$type;
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
      $this->headers[]= 'Set-Cookie: '.$cookie->getHeaderValue();
    }
    
    /**
     * Sets status code
     *
     * @param   int sc statuscode
     * @see     rfc://2616#10
     */
    public function setStatus($sc) {
      $this->statusCode= $sc;
    }
    
    /**
     * Checks whether headers have already been sent
     * to the client. In that case, one cannot trigger sending
     * of any header again.
     *
     * @return  bool
     */
    public function headersSent() {
      return headers_sent();
    }
    
    /**
     * Sends headers. The statuscode will be sent prior to any headers
     * and prefixed by HTTP/ and the <pre>version</pre> attribute.
     * 
     * Headers spanning multiple lines will be transformed to confirm
     *
     * @throws  lang.IllegalStateException if headers have already been sent
     */  
    public function sendHeaders() {
      if (headers_sent($file, $line))
        throw(new lang::IllegalStateException('Headers have already been sent at: '.$file.', line '.$line));
        
      switch (php_sapi_name()) {
        case 'cgi':
          header('Status: '.$this->statusCode);
          break;

        default:
          header(sprintf('HTTP/%s %d', $this->version, $this->statusCode));
      } 
      foreach ($this->headers as $header) {
        header(strtr($header, array("\r" => '', "\n" => "\n\t")), FALSE);
      }
    }

    /**
     * Tells the response to finish up whatever has to be finished up
     * (prepare for output) - in this base class, it does nothing. Have
     * a look at the XMLScriptletResponse to see what may be done here.
     * 
     * Of course, you can also override <pre>getContent()</pre> and
     * write your sourcecode there, but then, you won't be able to throw
     * any exceptions that can be caught by the main program when
     * calling the scriptlet's <pre>process</pre> method. So, if you have
     * critical stuff (such as XML/XSL-Transformations) which might fail,
     * it is probably a good idea to put them here.
     *
     * This function is *not* called in case any of the do*-functions 
     * return FALSE, indicating output processing is not needed (e.g.,
     * for relocates).
     *
     * @see     scriptlet.xml.XMLScriptletResponse#process
     */    
    public function process() {
    }
    
    /**
     * Sends content to STDOUT (which, on a webserver, is equivalent
     * to "send data to client").
     *
     */
    public function sendContent() {
      echo $this->getContent();
    }
    
    /**
     * Adds content to this response
     *
     * @param   string s string to add to the content
     */
    public function write($s) {
      $this->content.= $s;
    }
    
    /**
     * Sets content
     *
     * @param   string content Content
     */
    public function setContent($content) {
      $this->content= $content;
    }

    /**
     * Returns content
     *
     * @return  string Content
     */
    public function getContent() {
      return $this->content;
    }
  }
?>
