<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  namespace text::parser::generic;
 
  /**
   * ParserMessage
   *
   * @purpose  Value object
   */
  class ParserMessage extends lang::Object {
    public
      $code     = 0,
      $message  = '',
      $expected = array();

    /**
     * Constructor
     *
     * @param   int code
     * @param   string message
     * @param   string[] expected
     */
    public function __construct($code, $message, $expected) {
      $this->code= $code;
      $this->message= $message;
      $this->expected= $expected;
    }
  
    /**
     * Creates a string representation of this object
     * 
     * @return  string
     */
    public function toString() {
      return sprintf(
        '%s(%d: %s%s)',
        $this->getClassName(),
        $this->code,
        $this->message,
        $this->expected ? ', expected one of ['.implode(', ', $this->expected).']' : ''
      );
    }
  }
?>
