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
      $expect       = NULL,
      $errorcode    = '';
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string message
     * @param   string errorcode
     * @param   mixed actual default NULL
     * @param   mixed expect default NULL
     */
    function __construct($message, $errorcode, $actual= NULL, $expect= NULL) {
      parent::__construct($message);
      $this->errorcode= $errorcode;
      $this->actual= $actual;
      $this->expect= $expect;
    }
    
    /**
     * Set errorcode
     *
     * @access  public
     * @param   string errorcode
     */
    function setErrorCode($errorcode) {
      $this->errorcode= $errorcode;
    }

    /**
     * Get errorcode
     *
     * @access  public
     * @return  string
     */
    function getErrorCode() {
      return $this->errorcode;
    }
    
    /**
     * Retrieve string representation
     *
     * @access  public
     * @return  string
     */
    function toString() {
      $s= sprintf(
        "Exception %s (%s) {\n".
        "    errorcode:   %s\n".
        "    have:   [%s] %s\n".
        "    expect: [%s] %s\n".
        "  }\n",
        $this->getClassName(),
        $this->message,
        $this->errorcode,
        xp::typeOf($this->actual),
        var_export($this->actual, 1),
        xp::typeOf($this->expect),
        var_export($this->expect, 1)
      );
      for ($i= 0, $t= sizeof($this->trace); $i < $t; $i++) {
        $s.= $this->trace[$i]->toString();
      }
      return $s;
    }
  }
?>
