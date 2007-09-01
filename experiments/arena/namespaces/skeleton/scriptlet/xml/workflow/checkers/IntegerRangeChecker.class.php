<?php
/* This class is part of the XP framework
 *
 * $Id: IntegerRangeChecker.class.php 10438 2007-05-29 10:57:34Z friebe $ 
 */

  namespace scriptlet::xml::workflow::checkers;

  uses('scriptlet.xml.workflow.checkers.ParamChecker');

  /**
   * Checks whether given values are within an integer range
   *
   * Error codes returned are:
   * <ul>
   *   <li>toosmall - if the given value exceeds the lower boundary</li>
   *   <li>toolarge - if the given value exceeds the upper boundary</li>
   * </ul>
   *
   * @deprecated Use NumberRangeChecker instead
   * @see      xp://scriptlet.xml.workflow.checkers.NumberRangeChecker
   * @purpose  Checker
   */
  class IntegerRangeChecker extends ParamChecker {
    public
      $minValue  = 0,
      $maxValue  = 0;
    
    /**
     * Construct
     *
     * @param   int min
     * @param   int max
     */
    public function __construct($min, $max) {
      $this->minValue= $min;
      $this->maxValue= $max;
    }
    
    /**
     * Check a given value
     *
     * @param   array value
     * @return  string error or NULL on success
     */
    public function check($value) { 
      foreach ($value as $v) {
        if ($v < $this->minValue) {
          return 'toosmall';
        } else if ($v > $this->maxValue) {
          return 'toolarge';
        }
      }    
    }
  }
?>
