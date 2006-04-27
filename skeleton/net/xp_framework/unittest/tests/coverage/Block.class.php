<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.Collection');

  /**
   * Represents an Block
   *
   * @see      xp://net.xp_framework.unittest.tests.coverage.BlockTokenizerTest
   * @purpose  Helper class
   */
  class Block extends Object {
    var
      $line        = 0,
      $expressions = NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   net.xp_framework.unittest.tests.coverage.Expression[] expressions
     * @param   int line
     */
    function __construct($expressions, $line) {
      $this->line= $line;
      $this->expressions= &Collection::forClass('Fragment');
      $this->expressions->addAll($expressions);
    }
    
    /**
     * Checks if a specified object is equal to this object.
     *
     * @access  public
     * @param   &lang.Object block
     * @return  bool
     */
    function equals(&$block) {
      return (
        is('Block', $block) && 
        $this->line == $block->line &&
        $this->expressions->equals($block->expressions)
      );
    }

    /**
     * Creates a string representation of this object
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return $this->getClassName().'@({'.$this->expressions->toString().'} at line '.$this->line.')';
    }

  } implements(__FILE__, 'net.xp_framework.unittest.tests.coverage.Fragment');
?>
