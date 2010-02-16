<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('text.format.IFormat');
  
  /**
   * Binary data formatter
   *
   * @purpose  Provide a Format wrapper for binary strings
   * @see      php://addcslashes
   * @see      xp://text.format.IFormat
   */
  class BinaryFormat extends IFormat {

    /**
     * Get an instance
     *
     * @return  text.format.BinaryFormat
     */
    public function getInstance() {
      return parent::getInstance('BinaryFormat');
    }  
  
    /**
     * Apply format to argument
     *
     * @param   var fmt
     * @param   var argument
     * @return  string
     */
    public function apply($fmt, $argument) {
      if (!is_scalar($argument)) {
        throw new FormatException('Argument with type '.gettype($argument).' is not scalar');
      }
      return addcslashes($argument, "\0..\37!@\@\177..\377");
    }
  }
?>
