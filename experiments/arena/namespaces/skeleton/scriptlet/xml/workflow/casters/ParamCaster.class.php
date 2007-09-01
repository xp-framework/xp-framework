<?php
/* This class is part of the XP framework
 *
 * $Id: ParamCaster.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace scriptlet::xml::workflow::casters;

  /**
   * Casts given values
   *
   * @purpose  Abstract base class
   */
  class ParamCaster extends lang::Object {
  
    /**
     * Cast a given value
     *
     * @param   array value
     * @return  array value
     */
    public function castValue($value) { }
  }
?>
