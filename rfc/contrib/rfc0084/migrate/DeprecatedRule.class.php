<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('Rule');

  /**
   * Indicates something was deprecated and thus removed
   *
   * @purpose  Rule implementation
   */
  class DeprecatedRule extends Rule {
    var 
      $alternatives= array();
    
    /**
     * Constructor
     *
     * @access  public
     * @return  string[] alternatives default array()
     */
    function __construct($alternatives= array()) {
      $this->alternatives= $alternatives;
    }

    /**
     * Apply this rule to a given sourcecode 
     *
     * @access  public
     * @param   string package
     * @param   &text.String source
     * @return  &RuleApplyResult
     */
    function applyTo($package, $source) {
      $pattern= '/'.preg_quote($package).'/';
      if (0 != ($c= preg_match($pattern, $source->buffer))) {
        return new RuleApplyResult(FALSE, 0, 'Package '.$package.' cannot be migrated automatically');
      }
      return new RuleApplyResult(TRUE);
    }
    
    /**
     * Creates a string representation of this rule
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return ('deprecated without replacement.'.(empty($this->alternatives) 
        ? ''
        : 'Alternative APIs '.implode(', ', $this->alternatives)
      ));
    }
  }
?>
