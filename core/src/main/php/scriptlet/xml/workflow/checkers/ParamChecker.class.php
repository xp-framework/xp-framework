<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Checks given values
   *
   * @purpose  Abstract base class
   */
  abstract class ParamChecker extends Object {
  
    /**
     * Check a given value
     *
     * @param   array value
     * @return  string error or NULL on success
     */
    abstract public function check($value);
  }
?>
