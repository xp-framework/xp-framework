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
    public
      $statuscode = 0;

    /**
     * Constructor
     *
     * @access  public
     * @param   string message
     * @param   int statuscode
     */
    public function __construct($message, $statuscode= 0) {
      parent::__construct($message);
      $this->statuscode= $statuscode;
    }

    /**
     * Set statuscode
     *
     * @access  public
     * @param   int statuscode
     */
    public function setStatusCode($statuscode) {
      $this->statuscode= $statuscode;
    }

    /**
     * Get statuscode
     *
     * @access  public
     * @return  int
     */
    public function getStatusCode() {
      return $this->statuscode;
    }
    
    /**
     * Returns string representation
     *
     * @access  public
     * @return  string
     */
    public function toString() {
      $s= sprintf(
        "Exception %s (statuscode %d: %s)\n",
        $this->getClassName(),
        $this->statuscode,
        $this->message
      );
      for ($i= 0, $t= sizeof($this->trace); $i < $t; $i++) {
        $s.= $this->trace[$i]->toString();
      }
      return $s;
    }

  }
?>
