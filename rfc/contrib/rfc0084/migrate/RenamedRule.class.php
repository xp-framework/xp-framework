<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('Rule');

  /**
   * Indicates something was renamed in skeleton
   *
   * @purpose  Rule implementation
   */
  class RenamedRule extends Rule {
    var 
      $new= '';
    
    /**
     * Constructor
     *
     * @access  public
     * @return  string new new package
     */
    function __construct($new) {
      $this->new= $new;
    }

    /**
     * Creates a string representation of this rule
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return 'renamed to '.$this->new;
    }
  }
?>
