<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents an expression
   *
   * @purpose  Helper class
   */
  class Expression extends Object {
    var
      $code   = '',
      $start  = 0,
      $end    = 0;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string code
     * @param   int start the first line
     * @param   int end the last line
     */
    function __construct($code, $start, $end) {
      $this->code= $code;
      $this->start= $start;
      $this->end= $end;
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
        $this->start == $expr->start &&
        $this->end == $expr->end
      );
    }

    /**
     * Creates a string representation of this object
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return $this->getClassName().'@({'.$this->code.'} at lines '.$this->start.' - '.$this->end.')';
    }

  } implements(__FILE__, 'unittest.coverage.Fragment');
?>
