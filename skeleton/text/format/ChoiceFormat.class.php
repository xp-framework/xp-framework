<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.text.format.IFormat');
  
  /**
   * Choice formatter
   *
   * @purpose  Provide a Format wrapper for sprintf
   * @see      xp://util.text.format.Format
   */
  class ChoiceFormat extends IFormat {
  
    /**
     * Get an instance
     *
     * @access  public
     * @return  &util.text.format.MessageFormat
     */
    function &getInstance() {
      return parent::getInstance('ChoiceFormat');
    }  
  
    /**
     * Apply format to argument
     *
     * @access  public
     * @param   mixed fmt
     * @param   &mixed argument
     * @return  string
     * @throws  FormatException
     */
    function apply($fmt, &$argument) {
      foreach (explode('|', $fmt) as $choice) {
        list($cmp, $val)= explode(':', $choice);
        if ($argument == $cmp) {
          return $val;
        }
        if ('*' == $cmp) {
          return $val;
        }
      }
      return throw(new FormatException('Value is out of bounds'));
    }
  }
?>
