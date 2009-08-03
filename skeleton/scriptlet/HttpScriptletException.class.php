<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'peer.http.HttpConstants',
    'lang.ChainedException',
    'scriptlet.HttpScriptletResponse'
  );

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
  class HttpScriptletException extends ChainedException {
    public
      $response     = NULL,
      $statusCode   = 0;
      
    /**
     * Constructor
     *
     * @param   string message
     * @param   int statusCode default HttpConstants::STATUS_INTERNAL_SERVER_ERROR
     */
    public function __construct($message, $statusCode= HttpConstants::STATUS_INTERNAL_SERVER_ERROR, $cause= NULL) {
      parent::__construct($message, $cause);
      $this->statusCode= $statusCode;
      $this->_response($statusCode);
    }
    
    /**
     * Retrieve response
     *
     * @return  scriptlet.HttpScriptletResponse response object
     */
    public function getResponse() {
      return $this->response;
    }

    /**
     * Return compound message of this exception.
     *
     * @return  string
     */
    public function compoundMessage() {
      return sprintf(
        "Exception %s (%d:%s)",
        $this->getClassName(),
        $this->response->statusCode,
        $this->message
      );
    }
    
    /**
     * Create the response object
     *
     * @param   int statusCode
     */
    protected function _response($statusCode) {
      $this->response= new HttpScriptletResponse();
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
