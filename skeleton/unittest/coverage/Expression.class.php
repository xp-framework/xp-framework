<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.coverage.Fragment');

  /**
   * Represents an expression
   *
   * @purpose  Helper class
   */
  class Expression extends Object implements Fragment {
    public
      $code   = '',
      $start  = 0,
      $end    = 0;
      
    /**
     * Constructor
     *
     * @param   string code
     * @param   int start the first line
     * @param   int end the last line
     */
    public function __construct($code, $start, $end) {
      $this->code= $code;
      $this->start= $start;
      $this->end= $end;
    }
    
    /**
     * Checks if a specified object is equal to this object.
     *
     * @param   lang.Object expr
     * @return  bool
     */
    public function equals($expr) {
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
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'@({'.$this->code.'} at lines '.$this->start.' - '.$this->end.')';
    }

  } 
?>
