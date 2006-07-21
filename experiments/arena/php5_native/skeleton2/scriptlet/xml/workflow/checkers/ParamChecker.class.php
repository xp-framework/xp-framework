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
  class ParamChecker extends Object {
  
    /**
     * Check a given value
     *
     * @model   abstract
     * @access  public
     * @param   array value
     * @return  string error or NULL on success
     */
    public function check($value) { }
  }
?>
