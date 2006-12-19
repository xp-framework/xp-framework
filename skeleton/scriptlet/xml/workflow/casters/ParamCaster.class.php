<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Casts given values
   *
   * @purpose  Abstract base class
   */
  class ParamCaster extends Object {
  
    /**
     * Cast a given value
     *
     * @model   abstract
     * @access  public
     * @param   array value
     * @return  array value
     */
    public function castValue($value) { }
  }
?>
