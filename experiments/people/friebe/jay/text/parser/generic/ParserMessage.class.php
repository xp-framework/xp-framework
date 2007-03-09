<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  /**
   * ParserMessage
   *
   * @purpose  Value object
   */
  class ParserMessage extends Object {
    public
      $code    = 0,
      $message = '';

    /**
     * Constructor
     *
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
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'('.$this->code.') {"'.$this->message.'"}';
    }
  }
?>
