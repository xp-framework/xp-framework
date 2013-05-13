<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('scriptlet.xml.workflow.casters.ParamCaster');
  
  /**
   * Casts given values to floating point numbers
   *
   * @test xp://net.xp_framework.unittest.scriptlet.workflow.ToDoubleTest
   */
  class ToDouble extends ParamCaster {
  
    /**
     * Cast a given value.
     *
     * @see     php://intval
     * @see     xp://scriptlet.xml.workflow.casters.ParamCaster
     * @param   string[] value
     * @return  double[] value
     */
    public function castValue($value) {
      $return= array();
      foreach ($value as $k => $v) {
        if ('' == ltrim($v, ' +-0')) {
          $return[$k]= 0.0;
        } else {
          $return[$k]= floatval(strtr($v, ',', '.'));
        }
      }
      return $return;
    }
  }
?>
