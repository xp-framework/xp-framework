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
      $expect       = NULL,
      $cause         = '';
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string message
     * @param   string cause
     * @param   mixed actual default NULL
     * @param   mixed expect default NULL
     */
    public function __construct($message, $cause, $actual= NULL, $expect= NULL) {
      parent::__construct($message);
      $this->cause= $cause;
      $this->actual= $actual;
      $this->expect= $expect;
    }
    
    /**
     * Set cause
     *
     * @access  public
     * @param   string cause
     */
    public function setCause($cause) {
      $this->cause= $cause;
    }

    /**
     * Get cause
     *
     * @access  public
     * @return  string
     */
    public function getCause() {
      return $this->cause;
    }
    
    /**
     * Retrieve string representation
     *
     * @access  public
     * @return  string
     */
    public function toString() {
      $s= sprintf(
        "Exception %s (%s) {\n".
        "    cause:  %s\n".
        "    have:   [%s] %s\n".
        "    expect: [%s] %s\n".
        "  }\n",
        self::getClassName(),
        $this->message,
        $this->cause,
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
