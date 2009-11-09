<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('scriptlet.xml.workflow.casters.ParamCaster', 'util.Date');

  /**
   * Casts given values to date objects
   *
   * @test      xp://net.xp_framework.unittest.scriptlet.workflow.ToDateTest
   * @purpose   Caster
   */
  class ToDate extends ParamCaster {

    /**
     * Cast a given value
     *
     * @see     xp://scriptlet.xml.workflow.casters.ParamCaster
     * @param   array value
     * @return  array value
     */
    public function castValue($value) {
      $return= array();
      foreach ($value as $k => $v) {
        if ('' === $v) return 'empty';
        
        $pv= date_parse($v);
        if (
          !is_int($pv['year']) ||
          !is_int($pv['month']) ||
          !is_int($pv['day']) ||
          0 < $pv['warning_count'] ||
          0 < $pv['error_count']
        ) {
          return 'invalid';
        }
        
        try {
          $date= Date::create($pv['year'], $pv['month'], $pv['day'], $pv['hour'], $pv['minute'], $pv['second']);
        } catch (IllegalArgumentException $e) {
          return $e->getMessage();
        }

        $return[$k]= $date;
      }

      return $return;
    }
  }
?>
