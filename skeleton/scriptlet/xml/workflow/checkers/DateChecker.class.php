<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses('text.parser.DateParser');

  /**
   * Checks given date on validity
   *
   * Error codes returned are:
   * <ul>
   *   <li>invalid - if the given value is no valid date</li>
   * </ul>
   *
   * @purpose  Checker
   */
  class DateChecker extends Object {
    var
      $date  = NULL;
    
    /**
     * Construct
     *
     * @access  public
     */
    function __construct() { }

    /**
     * Check a given value
     *
     * @access  public
     * @param   array value
     * @return  string error or NULL on success
     */
    function check($value) { 
      foreach ($value as $v) {
        try(); {
          $date=  &DateParser::parse($v);
        } if (catch('FormatException', $e)) {
          return 'invalid';
        }
      }    
    }
  }
?>
