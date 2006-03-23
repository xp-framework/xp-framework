<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('scriptlet.HttpScriptletResponse');

  /**
   * Defines an exception which is thrown inside an HttpScriptlet
   * when it encounters a fatal error situation.
   *
   * These might be
   * <ul>
   *   <li>Session initialization fails</li>
   *   <li>HTTP method is not supported (e.g., DELETE)</li>
   *   <li>Request processing fails with an Exception</li>
   * </ul>
   *
   * @see      xp://scriptlet.HttpScriptlet#process
   * @purpose  Exception
   */  
  class HttpScriptletException extends Exception {
    var
      $response     = NULL,
      $statusCode   = 0;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string message
     * @param   int statusCode default HTTP_INTERNAL_SERVER_ERROR
     */
    function __construct($message, $statusCode= HTTP_INTERNAL_SERVER_ERROR) {
      parent::__construct($message);
      $this->statusCode= $statusCode;
      $this->_response($statusCode);
    }
    
    /**
     * Retrieve response
     *
     * @access  public
     * @return  &scriptlet.HttpScriptletResponse response object
     */
    function &getResponse() {
      return $this->response;
    }
    
    /**
     * Return formatted output of stacktrace
     *
     * @access  public
     * @return  string
     */
    function toString() {
      $s= sprintf(
        "Exception %s (%d:%s)\n",
        $this->getClassName(),
        $this->response->statusCode,
        $this->message
      );
      for ($i= 0, $t= sizeof($this->trace); $i < $t; $i++) {
        $s.= $this->trace[$i]->toString();
      }
      return $s;
    }
    
    /**
     * Create the response object
     *
     * @access  protected
     * @param   int statusCode
     */
    function _response($statusCode) {
      $this->response= &new HttpScriptletResponse();
      $this->response->setStatus($statusCode);
      $this->response->setContent(sprintf(
        "<h1>HTTP/1.1 %d %s</h1>\n<xmp>\n%s</xmp>\n",
        $statusCode,
        $this->message,
        $this->toString()
      ));
    }
  }
?>
