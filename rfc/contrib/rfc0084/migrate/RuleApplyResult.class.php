<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Result of a rule application
   *
   * @purpose  Value object
   */
  class RuleApplyResult extends Object {
    var
      $success= TRUE,
      $changes= 0,
      $message= '';

    /**
     * Constructor
     *
     * @access  public
     * @param   bool success
     * @param   int changes default 0 number of changes
     * @param   string message default NULL
     */
    function __construct($success, $changes= 0, $message= NULL) {
      $this->success= $success;
      $this->changes= $changes;
      $this->message= $message;
    }
    
    /**
     * Returns whether changes have occurred
     *
     * @access  public
     * @return  bool
     */
    function changesOccured() {
      return $this->changes > 0;
    }
    
    /**
     * Creates a string representation of this result
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return sprintf(
        'Rule applied(%s w/ %d changes, "%s")',
        $this->success ? 'success' : 'failure',
        $this->changes,
        $this->message
      );
    }
  }
?>
