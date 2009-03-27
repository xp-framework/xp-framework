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
     * Return compound message of this exception.
     *
     * @return  string
     */
    public function compoundMessage() {
      return sprintf(
        "%s (%s) { expected: [%s:%s] but was: [%s:%s] }\n",
        $this->getClassName(),
        $this->message,
        xp::typeOf($this->expect), xp::stringOf($this->expect),
        xp::typeOf($this->actual), xp::stringOf($this->actual)
      );
    }
    
    /**
     * Retrieve string representation
     *
     * @return  string
     */
    public function toString() {
      $s= $this->compoundMessage();
      
      // Slice first stack trace element, this is always unittest.TestCase::fail()
      // Also don't show the arguments
      for ($i= 1, $t= sizeof($this->trace); $i < $t; $i++) {
        $this->trace[$i]->args= NULL;
        $s.= $this->trace[$i]->toString();
      }
      return $s;
    }
  }
?>
