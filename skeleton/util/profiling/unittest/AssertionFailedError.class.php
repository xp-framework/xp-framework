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
      $actual       = NULL,
      $code         = '';
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string message
     * @param   mixed actual
     * @param   string code
     */
    function __construct($message, $actual, $code) {
      parent::__construct($message);
      $this->actual= $actual;
      $this->code= $code;
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
     * Retrieve string representation
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return parent::toString().sprintf(
        "\n  [code]     %s".
        "\n  [actual]   %s",
        $this->code,
        var_export($this->actual, 1)
      );
    }
  }
?>
