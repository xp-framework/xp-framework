<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.text.format.IFormat');
  
  /**
   * Printf formatter
   *
   * @purpose  Provide a Format wrapper for sprintf
   * @see      php://sprintf
   * @see      xp://util.text.format.IFormat
   */
  class PrintfFormat extends IFormat {

    /**
     * Get an instance
     *
     * @access  public
     * @return  &util.text.format.PrintfFormat
     */
    function &getInstance() {
      return parent::getInstance('PrintfFormat');
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
      switch (gettype($argument)) {
        case 'array':
          return vsprintf($fmt, array_values($argument));

        case 'object':
          return vsprintf($fmt, array_values(get_object_vars($argument)));

        default:
          return sprintf($fmt, $argument);
      }
    }
  }
?>
