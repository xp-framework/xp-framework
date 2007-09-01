<?php
/* This class is part of the XP framework
 *
 * $Id: ToInteger.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace scriptlet::xml::workflow::casters;

  uses('scriptlet.xml.workflow.casters.ParamCaster');
  
  /**
   * Casts given values to integers
   *
   * @purpose  Caster
   */
  class ToInteger extends ParamCaster {
  
    /**
     * Cast a given value.
     *
     * @see     php://intval
     * @see     xp://scriptlet.xml.workflow.casters.ParamCaster
     * @param   array value
     * @return  array value
     */
    public function castValue($value) {
      $return= array();
      foreach ($value as $k => $v) {
        if ('' == ltrim($v, ' +-0')) {
          $return[$k]= 0;
        } else {
          if (0 == ($return[$k]= intval($v))) return NULL;
        }
      }

      return $return;
    }
  }
?>
