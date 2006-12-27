<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('scriptlet.xml.workflow.checkers.ParamChecker');

  /**
   * Checks given values for a valid selection
   *
   * Error codes returned are:
   * <ul>
   *   <li>invalidoption - if the given value is invalid</li>
   * </ul>
   *
   * @purpose  Checker
   */
  class OptionChecker extends ParamChecker {
    public
      $validOptions = array();
    
    /**
     * Construct
     *
     * @param   array validOptions
     */
    public function __construct($validOptions) {
      $this->validOptions= $validOptions;
    }
    
    /**
     * Check a given value
     *
     * @param   array value
     * @return  string error or NULL on success
     */
    public function check($value) {
      foreach ($value as $v) {
        if (!in_array($v, $this->validOptions)) return 'invalidoption';
      }    
    }
  }
?>
