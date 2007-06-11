<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('text.format.IFormat');
  
  /**
   * Hash formatter
   *
   * @purpose  Provide a Format wrapper for Hashs
   * @see      xp://text.format.IFormat
   */
  class HashFormat extends IFormat {

    /**
     * Get an instance
     *
     * @return  text.format.HashFormat
     */
    public function getInstance() {
      return parent::getInstance('HashFormat');
    }  
  
    /**
     * Apply format to argument
     *
     * @param   mixed fmt
     * @param   mixed argument
     * @return  string
     */
    public function apply($fmt, $argument) {
      if (is_scalar($argument)) {
        throw(new FormatException('Argument with type '.gettype($argument).' is not an array or object'));
      }
      if (is('Hashmap', $argument)) {
        $hash= $argument->_hash;
      } else if (is_object($argument)) {
        $hash= get_object_vars($argument);
      } else {
        $hash= $argument;
      }
      
      $ret= '';
      $fmt= strtr($fmt, array(
        '\r'    => "\r",
        '\n'    => "\n",
        '\t'    => "\t"
      ));
      foreach (array_keys($argument) as $key) {
        $ret.= sprintf($fmt, $key, $argument[$key]);
      }
      return $ret;
    }
  }
?>
