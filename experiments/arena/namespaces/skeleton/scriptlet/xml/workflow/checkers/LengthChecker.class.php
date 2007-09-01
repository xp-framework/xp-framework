<?php
/* This class is part of the XP framework
 *
 * $Id: LengthChecker.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace scriptlet::xml::workflow::checkers;

  uses('scriptlet.xml.workflow.checkers.ParamChecker');

  /**
   * Checks given values for string length
   *
   * Error codes returned are:
   * <ul>
   *   <li>tooshort - if the given value's length is smaller than allowed</li>
   *   <li>toolong - if the given value's length is greater than allowed</li>
   * </ul>
   *
   * @purpose  Checker
   */
  class LengthChecker extends ParamChecker {
    public
      $minLength  = 0,
      $maxLength  = 0;
    
    /**
     * Construct
     *
     * @param   int min
     * @param   int max default -1
     */
    public function __construct($min, $max= -1) {
      $this->minLength= $min;
      $this->maxLength= $max;
    }
    
    /**
     * Check a given value
     *
     * @param   array value
     * @return  string error or NULL on success
     */
    public function check($value) { 
      foreach ($value as $v) {
        if (strlen($v) < $this->minLength) {
          return 'tooshort';
        } else if (($this->maxLength > 0) && (strlen($v) > $this->maxLength)) {
          return 'toolong';
        }
      }    
    }
  }
?>
