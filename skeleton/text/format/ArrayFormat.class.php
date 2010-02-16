<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

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
     * @param   var fmt
     * @param   var argument
     * @return  string
     */
    public function apply($fmt, $argument) {
      if (!is_array($argument)) {
        throw new FormatException('Argument with type '.gettype($argument).' is not an array');
      }
      
      return implode($fmt, $argument);
    }
  }
?>
