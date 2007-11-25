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
  class AssertionFailedError extends XPException {
    public
      $actual       = NULL,
      $expect       = NULL;
      
    /**
     * Constructor
     *
     * @param   string message
     * @param   string errorcode
     * @param   mixed actual default NULL
     * @param   mixed expect default NULL
     */
    public function __construct($message, $actual= NULL, $expect= NULL) {
      parent::__construct($message);
      $this->actual= $actual;
      $this->expect= $expect;
    }

    /**
     * Set errorcode
     *
     * @param   string errorcode
     */
    public function setErrorCode($errorcode) {
      $this->errorcode= $errorcode;
    }

    /**
     * Get errorcode
     *
     * @return  string
     */
    public function getErrorCode() {
      return $this->errorcode;
    }

    /**
     * Return compound message of this exception.
     *
     * @return  string
     */
    public function compoundMessage() {
      return sprintf(
        "%s (%s) { expected: [%s] but was: [%s] }\n",
        $this->getClassName(),
        $this->message,
        xp::stringOf($this->expect),
        xp::stringOf($this->actual)
      );
    }
    
    /**
     * Retrieve string representation
     *
     * @return  string
     */
    public function toString() {
      $s= $this->compoundMessage();
      
      // Slice the first four trace elements, they contain the
      // traces of assert() callbacks which aren't really interesting
      //
      // Also don't show the arguments
      for ($i= 3, $t= sizeof($this->trace); $i < $t; $i++) {
        $this->trace[$i]->args= NULL;
        $s.= $this->trace[$i]->toString();
      }
      return $s;
    }
  }
?>
