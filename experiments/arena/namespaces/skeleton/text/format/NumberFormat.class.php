<?php
/* This class is part of the XP framework
 *
 * $Id: NumberFormat.class.php 10594 2007-06-11 10:04:54Z friebe $
 */

  namespace text::format;

  uses('text.format.IFormat');
  
  /**
   * Printf formatter
   *
   * @purpose  Provide a Format wrapper for numbers
   * @see      php://number_format
   * @see      php://localeconv
   * @see      xp://text.format.IFormat
   */
  class NumberFormat extends IFormat {

    /**
     * Get an instance
     *
     * @return  text.format.NumberFormat
     */
    public function getInstance() {
      return parent::getInstance('NumberFormat');
    }  
  
    /**
     * Apply format to argument
     *
     * @param   mixed fmt
     * @param   mixed argument
     * @return  string
     * @throws  lang.FormatException
     */
    public function apply($fmt, $argument) {
      if (!is_numeric($argument)) {
        throw(new lang::FormatException('Argument '.$argument.' of type "'.gettype($argument).'" is not a number'));
      }
      
      list($decimals, $dec_point, $thousands_sep)= explode('#', $fmt);
      return number_format(
        floatval($argument), 
        $decimals, 
        $dec_point,
        $thousands_sep
      );
    }
  }
?>
