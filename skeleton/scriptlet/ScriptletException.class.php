<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'peer.http.HttpConstants',
    'lang.ChainedException'
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
   * @see      xp://scriptlet.HttpScriptlet#service
   */  
  class ScriptletException extends ChainedException {
    public $statusCode= 0;
      
    /**
     * Constructor
     *
     * @param   string message
     * @param   int statusCode default HttpConstants::STATUS_INTERNAL_SERVER_ERROR
     * @param   lang.Throwable cause
     */
    public function __construct($message, $statusCode= HttpConstants::STATUS_INTERNAL_SERVER_ERROR, $cause= NULL) {
      parent::__construct($message, $cause);
      $this->statusCode= $statusCode;
    }
    
    /**
     * Retrieve statusCode
     *
     * @return  int
     */
    public function getStatus() {
      return $this->statusCode;
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
        $this->statusCode,
        $this->message
      );
    }
  }
?>
