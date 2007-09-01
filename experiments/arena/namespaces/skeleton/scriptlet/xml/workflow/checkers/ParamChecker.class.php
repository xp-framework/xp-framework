<?php
/* This class is part of the XP framework
 *
 * $Id: ParamChecker.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace scriptlet::xml::workflow::checkers;

  /**
   * Checks given values
   *
   * @purpose  Abstract base class
   */
  class ParamChecker extends lang::Object {
  
    /**
     * Check a given value
     *
     * @param   array value
     * @return  string error or NULL on success
     */
    public function check($value) { }
  }
?>
