<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.text.format.IFormat');
  
  /**
   * Money formatter
   *
   * @purpose  Provide a Format wrapper for money_format
   * @see      php://money_format
   * @see      xp://util.text.format.IFormat
   */
  class MoneyFormat extends IFormat {

    /**
     * Get an instance
     *
     * @access  public
     * @return  &util.text.format.MoneyFormat
     */
    function &getInstance() {
      return parent::getInstance('MoneyFormat');
    }  
  
    /**
     * Apply format to argument
     *
     * @access  public
     * @param   mixed fmt
     * @param   &mixed argument
     * @return  string
     */
    function apply($fmt, &$argument) {
      if (!function_exists('money_format')) {
        return throw(new FormatException('money_format requires PHP >= 4.3.0'));
      }
      return money_format($fmt, $argument);
    }
  }
?>
