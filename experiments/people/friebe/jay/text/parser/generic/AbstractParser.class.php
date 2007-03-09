<?php
/* This file is part of the XP framework
 *
 * $Id$
 */

  uses(
    'text.parser.generic.ParseException',
    'text.parser.generic.ParserMessage',
    'util.log.Traceable'
  );

  /**
   * Abstract parser. Subclasses of this class are generated!
   *
   * @purpose   Base class
   */
  abstract class AbstractParser extends Object {
    public
      $cat          = NULL,
      $errors       = array();

    /**
     * Adds an error
     *
     * @param   text.parser.generic.ParseException error
     */
    public function addError($error) {
      $this->errors[]= $error;
    }
    
    /**
     * Returns whether errors have occured
     *
     * @return  bool
     */
    public function hasErrors() {
      return !empty($this->errors);
    }

    /**
     * Returns whether errors have occured
     *
     * @return  text.parser.generic.ParseException[]
     */
    public function getErrors() {
      return $this->errors;
    }

    /**
     * Error handler
     *
     * @param   int level
     * @param   string message
     * @param   string[] expected
     */
    public function error($level, $message, $expected= array()) {
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
     * @param   util.log.LogCategory cat
     */
    public function setTrace($cat) {
      $this->cat= $cat;
    }
    
    /**
     * Parse
     *
     * @param   text.parser.generic.AbstractLexer lexer
     * @return  mixed result of the last reduction, if any.
     * @throws  text.parser.generic.ParseException if an exception occurs during parsing.
     */
    public function parse($lexer) {
      $this->errors= array();

      try {
        $result= $this->yyparse($lexer);
      } catch (Throwable $e) {
        throw new ParseException($e->getMessage(), $e);
      }
      
      if (!empty($this->errors)) {
        $s= '';
        foreach ($this->getErrors() as $error) {
          $s.= '- '.$error->toString()."\n";
        }
        throw new ParseException(sizeof($this->errors).' error(s)', new FormatException($s));
      }
      
      return $result;
    }
    
    /**
     * Parser main method. Maintains a state and a value stack, 
     * currently with fixed maximum size.
     *
     * @param   text.parser.generic.AbstractLexer lexer
.    * @return  mixed result of the last reduction, if any.
     */
    public abstract function yyparse($lexer);
    
    /**
     * Retrieves name of a given token
     *
     * @param   int token
     * @return  string name
     */
    protected abstract  function yyname($token);
    
    /**
     * Computes list of expected tokens on error by tracing the tables.
     *
     * @param   int state for which to compute the list.
     * @return  string[] list of token names.
     */
    protected abstract function yyexpecting($state);

  }
?>
