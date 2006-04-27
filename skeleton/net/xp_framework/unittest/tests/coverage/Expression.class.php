<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents an expression
   *
   * @see      xp://net.xp_framework.unittest.tests.coverage.ExpressionTokenizerTest
   * @purpose  Helper class
   */
  class Expression extends Object {
    var
      $code = '',
      $line = 0;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string code
     * @param   int line
     */
    function __construct($code, $line) {
      $this->code= $code;
      $this->line= $line;
    }
    
    /**
     * Checks if a specified object is equal to this object.
     *
     * @access  public
     * @param   &lang.Object expr
     * @return  bool
     */
    function equals(&$expr) {
      return (
        is('Expression', $expr) && 
        $this->code == $expr->code &&
        $this->line == $expr->line
      );
    }
  }
?>
