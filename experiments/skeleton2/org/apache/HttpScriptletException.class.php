<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('org.apache.HttpScriptletResponse');

  /**
   * Defines an exception which is thrown inside an HttpScriptlet
   * when it encounters a fatal error situation.
   *
   * These might be:
   * - Session initialization fails
   * - HTTP method is not supported (e.g., DELETE)
   * - Request processing fails with an Exception
   *
   * @see   xp://org.apache.HttpScriptlet#process
   */  
  class HttpScriptletException extends XPException {
    public
      $response;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string message
     * @param   int statusCode default HTTP_INTERNAL_SERVER_ERROR
     */
    public function __construct($message, $statusCode= HTTP_INTERNAL_SERVER_ERROR) {
      parent::__construct($message);
      self::_response($statusCode);
    }
    
    /**
     * Retrieve response
     *
     * @access  public
     * @return  org.apache.HttpScriptletResponse response object
     */
    public function getResponse() {
      return $this->response;
    }
    
    /**
     * Create the response object
     *
     * @access  private
     * @param   int statusCode
     */
    private function _response($statusCode) {
      $this->response= new HttpScriptletResponse();
      $this->response->statusCode= $statusCode;
      $this->response->setContent(sprintf(
        "<h1>Internal Server Error</h1>\n<xmp>\n%s</xmp>\n",
        self::toString()
      ));
    }
  }
?>
