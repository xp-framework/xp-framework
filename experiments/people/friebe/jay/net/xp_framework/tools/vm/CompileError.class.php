<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$
 */
 
  /**
   * Compile error
   *
   * @purpose  Exception
   */
  class CompileError extends Object {
    public 
      $code    = 0,
      $message = '';

    /**
     * Constructor
     *
     * @access  public
     * @param   int code
     * @param   string message
     */
    public function __construct($code, $message) {
      $this->code= $code;
      $this->message= $message;
    }
  
    /**
     * Creates a string representation of this object
     * 
     * @access  public
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'('.$this->code.') {"'.$this->message.'"}';
    }
  }
?>
