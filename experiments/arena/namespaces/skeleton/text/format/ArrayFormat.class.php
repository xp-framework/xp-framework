<?php
/* This class is part of the XP framework
 *
 * $Id: ArrayFormat.class.php 10594 2007-06-11 10:04:54Z friebe $
 */

  namespace text::format;

  uses('text.format.IFormat');
  
  /**
   * Array formatter
   *
   * @purpose  Provide a Format wrapper for arrays
   * @see      xp://text.format.IFormat
   */
  class ArrayFormat extends IFormat {

    /**
     * Get an instance
     *
     * @return  text.format.ArrayFormat
     */
    public function getInstance() {
      return parent::getInstance('ArrayFormat');
    }  
  
    /**
     * Apply format to argument
     *
     * @param   mixed fmt
     * @param   mixed argument
     * @return  string
     */
    public function apply($fmt, $argument) {
      if (!is_array($argument)) {
        throw(new lang::FormatException('Argument with type '.gettype($argument).' is not an array'));
      }
      
      return implode($fmt, $argument);
    }
  }
?>
