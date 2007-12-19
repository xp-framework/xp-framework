<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.Collection', 'util.collections.Vector', 'unittest.coverage.Fragment');

  /**
   * Represents an Block
   *
   * @purpose  Helper class
   */
  class Block extends Object implements Fragment {
    public
      $code        = '',
      $start       = 0,
      $end         = 0,
      $expressions = NULL;
      
    /**
     * Constructor
     *
     * @param   unittest.coverage.Expression[] expressions
     * @param   int start the first line
     * @param   int end the last line
     */
    public function __construct($code, $expressions, $start, $end) {
      $this->code= $code;
      $this->start= $start;
      $this->end= $end;
      $this->expressions= create('new util.collections.Vector<unittest.coverage.Fragment>()');
      foreach ($expressions as $expr) {
        $this->expressions->add($expr);
      }
    }
    
    /**
     * Checks if a specified object is equal to this object.
     *
     * @param   lang.Generic cmp
     * @return  bool
     */
    public function equals($cmp) {
      return (
        $cmp instanceof self && 
        $this->start === $cmp->start &&
        $this->end === $cmp->end &&
        $this->code === $cmp->code &&
        $this->expressions->equals($cmp->expressions)
      );
    }

    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'@({'.$this->code.' {'.str_replace("\n", "\n  ", $this->expressions->toString()).'}} at lines '.$this->start.' - '.$this->end.')';
    }

  } 
?>
