<?php
/* This file is part of the XP framework
 *
 * $Id$
 */

  uses(
    'text.parser.generic.ParseException',
    'text.parser.generic.ParserMessage'
  );

  /**
   * Abstract parser. Subclasses of this class are generated!
   *
   * @purpose   Base class
   */
  class AbstractParser extends Object {
    var
      $cat          = NULL,
      $errors       = array();

    /**
     * Adds an error
     *
     * @access  public
     * @param   &text.parser.generic.ParseException error
     */
    function addError(&$error) {
      $this->errors[]= &$error;
    }
    
    /**
     * Returns whether errors have occured
     *
     * @access  public
     * @return  bool
     */
    function hasErrors() {
      return !empty($this->errors);
    }

    /**
     * Returns whether errors have occured
     *
     * @access  public
     * @return  text.parser.generic.ParseException[]
     */
    function getErrors() {
      return $this->errors;
    }

    /**
     * Error handler
     *
     * @access  public
     * @param   int level
     * @param   string message
     * @param   string[] expected
     */
    function error($level, $message, $expected= array()) {
      switch ($level) {
        case E_PARSE:
        case E_ERROR:
        case E_CORE_ERROR:
        case E_COMPILE_ERROR:
          $this->addError(new ParserMessage(
            $level, 
            $message.($expected ? ', expected '.implode(' or ', $expected) : '')
          ));
          // Fall-through intended
      }
      
      $this->cat && $this->cat->error($message, $expected ? ', expected '.implode(' or ', $expected) : '');
      return FALSE;
    }

    /**
     * Set a logger category for debugging
     *
     * @access  public
     * @param   util.log.LogCategory cat
     */
    function setTrace($cat) {
      $this->cat= $cat;
    }
    
    /**
     * Parse
     *
     * @access  public
     * @param   &text.parser.generic.AbstractLexer lexer
     * @return  mixed result of the last reduction, if any.
     * @throws  text.parser.generic.ParseException if an exception occurs during parsing.
     */
    function parse(&$lexer) {
      $this->errors= array();

      try(); {
        $result= &$this->yyparse($lexer);
      } if (catch('Exception', $e)) {
        return throw(new ParseException($e->getMessage(), $e));
      }
      
      if (!empty($this->errors)) {
        $s= '';
        foreach ($this->getErrors() as $error) {
          $s.= '- '.$error->toString()."\n";
        }
        return throw(new ParseException(sizeof($this->errors).' error(s)', new FormatException($s)));
      }
      
      return $result;
    }
    
    /**
     * Parser main method. Maintains a state and a value stack, 
     * currently with fixed maximum size.
     *
     * @model   abstract
     * @access  protected
     * @param   &text.parser.generic.AbstractLexer lexer
.    * @return  mixed result of the last reduction, if any.
     */
    function yyparse(&$lexer) { }
    
    /**
     * Retrieves name of a given token
     *
     * @model   abstract
     * @access  protected
     * @param   int token
     * @return  string name
     */
    function yyname($token) { }
    
    /**
     * Computes list of expected tokens on error by tracing the tables.
     *
     * @model   abstract
     * @access  protected
     * @param   int state for which to compute the list.
     * @return  string[] list of token names.
     */
    function yyexpecting($state) { }

  } implements(__FILE__, 'util.log.Traceable');
?>
