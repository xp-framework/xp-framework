<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Indicates an assertion failed
   *
   * @purpose  Exception
   */
  class AssertionFailedError extends Exception {
    var
      $actual   = NULL,
      $code     = '',
      $trace    = '';
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string message
     * @param   mixed actual
     * @param   string code
     */
    function __construct($message, $actual, $code) {
      $this->actual= $actual;
      $this->code= $code;
      parent::__construct($message);
    }
    
    /**
     * Get Trace
     *
     * @access  public
     * @return  string
     */
    function getStackTrace() {
      return $this->trace;
    }
    
    /**
     * Set Code
     *
     * @access  public
     * @param   string code
     */
    function setCode($code) {
      $this->code= $code;
    }

    /**
     * Get Code
     *
     * @access  public
     * @return  string
     */
    function getCode() {
      return $this->code;
    }

    /**
     * Set Trace
     *
     * @access  public
     * @param   string trace
     */
    function setTrace($trace) {
      $this->trace= $trace;
    }

    /**
     * Get Trace
     *
     * @access  public
     * @return  string
     */
    function getTrace() {
      return $this->trace;
    }
  }
?>
