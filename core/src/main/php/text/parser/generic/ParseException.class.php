<?php
/* This file is part of the XP framework
 *
 * $Id$
 */

  uses('text.parser.generic.ParserMessage');

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
     * @param lang.Throwable cause
     * @param text.parser.generic.ParserMessage[] errors 
     */
    public function __construct($message, $cause= NULL, $errors= array()) {
      parent::__construct($message, $cause);
      $this->errors= $errors;
    }
    
    /**
     * Sets the errors.
     * 
     * @param text.parser.generic.ParserMessage[] errors 
     */
    public function setErrors($errors) {
      $this->errors= $errors;
    }
    
    /**
     * Add a parse error message
     * 
     * @param text.parser.generic.ParserMessage errors 
     */
    public function addError(ParserMessage $error) {
      $this->errors[]= $error;
    }
    
    /**
     * Gets the parse error messages
     * 
     * @return text.parser.generic.ParserMessage[] 
     */
    public function getErrors() {
      return $this->errors;
    }
    
    /**
     * Gets the parse error messages formatted as a string list
     *
     * <pre>
     *   - Error 1
     *   - Error 2
     *   - ...
     * </pre>
     *
     * @param   string indent
     * @return  string
     */
    public function formattedErrors($indent= '  ') {
      $s= '';
      foreach ($this->errors as $error) {
        $s.= $indent.'- '.$error->toString()."\n";
      }
      return $s;
    }

    /**
     * Return compound message including all error messages.
     * 
     * @return string 
     */
    public function compoundMessage() {
      return sprintf(
        "Exception %s (%s)\n%s",
        $this->getClassName(),
        $this->message,
        $this->formattedErrors()
      );
    }
  }
?>
