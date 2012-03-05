<?php
/* This file is part of the XP framework
 *
 * $Id$
 */

  uses(
    'text.parser.generic.ParserMessage'
  );
  /**
   * Indicates an error occured during parsing
   *
   * @see       xp://text.parser.generic.AbstractParser#parse
   * @purpose   Exception
   */
  class ParseException extends XPException {
    private
      $errors= array();

    /**
     * Constructor
     * 
     * @param string message
     * @param Throwable cause
     * @param ParserMessage[] errors 
     */
    public function __construct($message, $cause= NULL, $errors= array()) {
      parent::__construct($message, $cause);
      
      $this->errors= $errors;
    }
    
    /**
     * Sets the errors.
     * 
     * @param ParserMessage[] errors 
     */
    public function setErrors($errors) {
      $this->errors= $errors;
    }
    
    /**
     * Add a parse error message
     * 
     * @param ParserMessage errors 
     */
    public function addError(ParserMessage $error) {
      $this->errors[]= $error;
    }
    
    /**
     * Gets the parse error messages
     * 
     * @return ParserMessage[] 
     */
    public function getErrors() {
      return $this->errors;
    }
    
    /**
     * Return compound message including all error messages.
     * 
     * @return string 
     */
    public function compoundMessage() {
      $s= '';
      foreach ($this->errors as $error) {
        $s.= '  - '.$error->toString()."\n";
      }
      return sprintf(
        "Exception %s (%s)\n%s",
        $this->getClassName(),
        $this->message,
        $s
      );
    }
  }
?>
