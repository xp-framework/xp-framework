<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('scriptlet.xml.workflow.checkers.ParamChecker');

  /**
   * Checks given values for pattern matches
   *
   * Error codes returned are:
   * <ul>
   *   <li>nomatch - if the given value doesn't match the regular expression</li>
   * </ul>
   *
   * @see      php://preg_match
   * @purpose  Checker
   */
  class RegexpChecker extends ParamChecker {
    var
      $pattern  = '';
    
    /**
     * Construct
     *
     * @access  public
     * @param   string pattern
     */
    function __construct($pattern) {
      $this->pattern= $pattern;
    }
    
    /**
     * Check a given value
     *
     * @access  public
     * @param   array value
     * @return  string error or NULL on success
     */
    function check($value) { 
      foreach ($value as $v) {
        if (!preg_match($pattern, $v)) return 'nomatch';
      }    
    }
  }
?>
