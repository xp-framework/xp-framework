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
      $code         = '';
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string message
     * @param   string code
     * @param   mixed actual default NULL
     * @param   mixed expect default NULL
     */
    public function __construct($message, $code, $actual= NULL, $expect= NULL) {
      parent::__construct($message);
      $this->code= $code;
      $this->actual= $actual;
      $this->expect= $expect;
    }
    
    /**
     * Set Code
     *
     * @access  public
     * @param   string code
     */
    public function setCode($code) {
      $this->code= $code;
    }

    /**
     * Get Code
     *
     * @access  public
     * @return  string
     */
    public function getCode() {
      return $this->code;
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
        "    code:   %s\n".
        "    have:   [%s] %s\n".
        "    expect: [%s] %s\n".
        "  }\n",
        self::getClassName(),
        $this->message,
        $this->code,
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
