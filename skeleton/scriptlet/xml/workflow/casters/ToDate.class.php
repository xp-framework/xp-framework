<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('scriptlet.xml.workflow.casters.ParamCaster', 'util.Date');

  /**
   * Casts given values to date objects
   *
   * @purpose  Caster
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
        try {
          $date= new Date($v);
        } catch (IllegalArgumentException $e) {
          return $e->getMessage();
        }

        $return[$k]= $date;
      }

      return $return;
    }
  }
?>
