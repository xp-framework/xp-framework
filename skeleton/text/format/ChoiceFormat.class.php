<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('text.format.IFormat');
  
  /**
   * Choice formatter
   *
   * @purpose  Provide a Format wrapper for values depending on choices
   * @see      xp://text.format.IFormat
   */
  class ChoiceFormat extends IFormat {
  
    /**
     * Get an instance
     *
     * @access  public
     * @return  &text.format.ChoiceFormat
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
     * @throws  lang.FormatException
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
