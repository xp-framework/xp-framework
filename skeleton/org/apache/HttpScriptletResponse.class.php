<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  // HTTP status codes
  define('HTTP_CONTINUE',                          100);
  define('HTTP_SWITCHING_PROTOCOLS',               101);
  define('HTTP_PROCESSING',                        102);
  define('HTTP_OK',                                200);
  define('HTTP_CREATED',                           201);
  define('HTTP_ACCEPTED',                          202);
  define('HTTP_NON_AUTHORITATIVE_INFORMATION ',    203);
  define('HTTP_NO_CONTENT',                        204);
  define('HTTP_RESET_CONTENT',                     205);
  define('HTTP_PARTIAL_CONTENT',                   206);
  define('HTTP_MULTI_STATUS',                      207);
  define('HTTP_MULTIPLE_CHOICES',                  300);
  define('HTTP_MOVED_PERMANENTLY',                 301);
  define('HTTP_FOUND',                             302);
  define('HTTP_SEE_OTHER',                         303);
  define('HTTP_NOT_MODIFIED',                      304);
  define('HTTP_USE_PROXY',                         305);
  define('HTTP_TEMPORARY_REDIRECT',                307);
  define('HTTP_BAD_REQUEST',                       400);
  define('HTTP_AUTHORIZATION_REQUIRED',            401);
  define('HTTP_PAYMENT_REQUIRED',                  402);
  define('HTTP_FORBIDDEN',                         403);
  define('HTTP_NOT_FOUND',                         404);
  define('HTTP_METHOD_NOT_ALLOWED',                405);
  define('HTTP_NOT_ACCEPTABLE',                    406);
  define('HTTP_PROXY_AUTHENTICATION_REQUIRED',     407);
  define('HTTP_REQUEST_TIME_OUT',                  408);
  define('HTTP_CONFLICT',                          409);
  define('HTTP_GONE',                              410);
  define('HTTP_LENGTH_REQUIRED',                   411);
  define('HTTP_PRECONDITION_FAILED',               412);
  define('HTTP_REQUEST_ENTITY_TOO_LARGE',          413);
  define('HTTP_REQUEST_URI_TOO_LARGE',             414);
  define('HTTP_UNSUPPORTED_MEDIA_TYPE',            415);
  define('HTTP_REQUESTED_RANGE_NOT_SATISFIABLE',   416);
  define('HTTP_EXPECTATION_FAILED',                417);
  define('HTTP_UNPROCESSABLE_ENTITY',              422);
  define('HTTP_LOCKED',                            423);
  define('HTTP_FAILED_DEPENDENCY',                 424);
  define('HTTP_INTERNAL_SERVER_ERROR',             500);
  define('HTTP_METHOD_NOT_IMPLEMENTED',            501);
  define('HTTP_BAD_GATEWAY',                       502);
  define('HTTP_SERVICE_TEMPORARILY_UNAVAILABLE',   503);
  define('HTTP_GATEWAY_TIME_OUT',                  504);
  define('HTTP_HTTP_VERSION_NOT_SUPPORTED',        505);
  define('HTTP_VARIANT_ALSO_NEGOTIATES',           506);
  define('HTTP_INSUFFICIENT_STORAGE',              507);
  define('HTTP_NOT_EXTENDED',                      510);

  /**
   * Defines the response sent from the webserver to the client,
   * consisting from response headers and an optional body.
   *
   * An instance of this object is passed to the do* methods by
   * the <pre>process</pre> method.
   *
   * @see      xp://org.apache.HttpScriptlet
   * @purpose  Provide a way to access the HTTP response
   */  
  class HttpScriptletResponse extends Object {
    var
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
     * @see     org.apache.HttpScriptlet#doCreateSession
     * @access  public
     * @param   string target an absolute URI
     */
    function sendRedirect($location) {
      $this->statusCode= HTTP_FOUND;
      $this->setHeader('Location', $location);
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
     * @access  public
     * @param   string name header name
     * @param   string value header value
     */
    function setHeader($name, $value) {
      $this->headers[$name]= $value;
    }
    
    /**
     * Sets status code
     *
     * @access  public
     * @param   int sc statuscode
     * @see     rfc://2616#10
     */
    function setStatus($sc) {
      $this->statusCode= $sc;
    }
    
    /**
     * Sends headers. The statuscode will be sent prior to any headers
     * and prefixed by HTTP/ and the <pre>version</pre> attribute.
     * 
     * Headers spanning multiple lines will be transformed to confirm
     *
     * @access  public
     */  
    function sendHeaders() {
      header(sprintf(
        'HTTP/%s %d', 
        $this->version, 
        $this->statusCode
      ));
      foreach (array_keys($this->headers) as $key) {
        header($key.': '.strtr(
          $this->headers[$key], 
          array("\r" => '', "\n" => "\n\t")
        ));
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
     * @see     org.apache.xml.XMLScriptletResponse#process
     * @access  public
     */    
    function process() {
    }
    
    /**
     * Sends content to STDOUT (which, on a webserver, is equivalent
     * to "send data to client").
     *
     * @access  public
     */
    function sendContent() {
      echo $this->getContent();
    }
    
    /**
     * Adds content to this response
     *
     * @access  public
     * @param   string s string to add to the content
     */
    function write($s) {
      $this->content.= $s;
    }
    
    /**
     * Sets content
     *
     * @access  public
     * @param   string content Content
     */
    function setContent($content) {
      $this->content= $content;
    }

    /**
     * Returns content
     *
     * @access  public
     * @return  string Content
     */
    function getContent() {
      return $this->content;
    }
  }
?>
