<?php
/* This class is part of the XP framework
 *
 * $Id: ToDate.class.php 9244 2007-01-11 17:40:18Z friebe $ 
 */

  namespace scriptlet::xml::workflow::casters;

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
     * @param   array value
     * @return  array value
     */
    public function castValue($value) {
      $return= array();
      foreach ($value as $k => $v) {
        try {
          $date= text::parser::DateParser::parse($v);
        } catch (lang::FormatException $e) {
          return $e->getMessage();
        } catch (lang::IllegalArgumentException $e) {
          return $e->getMessage();
        }
        
        $return[$k]= $date;
      }

      return $return;
    }
  }
?>
