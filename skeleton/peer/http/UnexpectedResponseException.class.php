<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Indicates the response was unexpected
   *
   * @see      xp://peer.http.HttpUtil
   * @purpose  Exception
   */
  class UnexpectedResponseException extends Exception {
    var
      $statuscode = 0;

    /**
     * Constructor
     *
     * @access  public
     * @param   string message
     * @param   int statuscode
     */
    function __construct($message, $statuscode= 0) {
      parent::__construct($message);
      $this->statuscode= $statuscode;
    }

    /**
     * Set statuscode
     *
     * @access  public
     * @param   int statuscode
     */
    function setStatusCode($statuscode) {
      $this->statuscode= $statuscode;
    }

    /**
     * Get statuscode
     *
     * @access  public
     * @return  int
     */
    function getStatusCode() {
      return $this->statuscode;
    }
    
    /**
     * Return compound message of this exception.
     *
     * @access  public
     * @return  string
     */
    function compoundMessage() {
      return sprintf(
        'Exception %s (statuscode %d: %s)',
        $this->getClassName(),
        $this->statuscode,
        $this->message
      );
    }
  }
?>
