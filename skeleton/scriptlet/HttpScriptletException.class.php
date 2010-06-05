<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'scriptlet.ScriptletException',
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
   * @deprecated  Use ScriptletException instead
   * @see      xp://scriptlet.ScriptletException
   */  
  class HttpScriptletException extends ScriptletException {
    public $response= NULL;
      
    /**
     * Constructor
     *
     * @param   string message
     * @param   int statusCode default HttpConstants::STATUS_INTERNAL_SERVER_ERROR
     */
    public function __construct($message, $statusCode= HttpConstants::STATUS_INTERNAL_SERVER_ERROR, $cause= NULL) {
      parent::__construct($message, $statusCode, $cause);
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
