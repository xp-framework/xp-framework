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
