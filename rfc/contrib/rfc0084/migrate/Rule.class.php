<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('RuleApplyResult');

  /**
   * Base class for all other rules
   *
   * @purpose  Base class
   */
  class Rule extends Object {
  
    /**
     * Apply this rule to a given sourcecode 
     *
     * @model   abstract
     * @access  public
     * @param   string package
     * @param   &text.String source
     * @return  &RuleApplyResult
     */
    function applyTo($package, $source) { }
  }
?>
