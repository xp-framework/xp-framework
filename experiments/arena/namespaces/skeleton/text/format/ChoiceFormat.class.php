<?php
/* This class is part of the XP framework
 *
 * $Id: ChoiceFormat.class.php 10594 2007-06-11 10:04:54Z friebe $
 */

  namespace text::format;

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
     * @return  text.format.ChoiceFormat
     */
    public function getInstance() {
      return parent::getInstance('ChoiceFormat');
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
      foreach (explode('|', $fmt) as $choice) {
        list($cmp, $val)= explode(':', $choice);
        if ($argument == $cmp) {
          return $val;
        }
        if ('*' == $cmp) {
          return $val;
        }
      }
      throw(new lang::FormatException('Value is out of bounds'));
    }
  }
?>
