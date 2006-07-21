<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('scriptlet.xml.workflow.casters.ParamCaster', 'text.parser.DateParser');
  
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
     * @access  public
     * @param   array value
     * @return  array value
     */
    public function castValue($value) {
      $return= array();
      foreach ($value as $k => $v) {
        try {
          $date= &DateParser::parse($v);
        } catch (FormatException $e) {
          return $e->getMessage();
        }
        
        $return[$k]= &$date;
      }

      return $return;
    }
  }
?>
