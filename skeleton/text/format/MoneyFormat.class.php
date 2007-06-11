<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('text.format.IFormat');
  
  /**
   * Money formatter
   *
   * @purpose  Provide a Format wrapper for money_format
   * @see      php://money_format
   * @see      xp://text.format.IFormat
   */
  class MoneyFormat extends IFormat {

    /**
     * Get an instance
     *
     * @return  text.format.MoneyFormat
     */
    public function getInstance() {
      return parent::getInstance('MoneyFormat');
    }  
  
    /**
     * Apply format to argument
     *
     * @param   mixed fmt
     * @param   mixed argument
     * @return  string
     */
    public function apply($fmt, $argument) {
      if (!function_exists('money_format')) {
        throw(new FormatException('money_format requires PHP >= 4.3.0'));
      }
      return money_format($fmt, $argument);
    }
  }
?>
