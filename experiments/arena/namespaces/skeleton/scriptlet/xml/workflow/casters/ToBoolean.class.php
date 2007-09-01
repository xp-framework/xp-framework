<?php
/* This class is part of the XP framework
 *
 * $Id: ToBoolean.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace scriptlet::xml::workflow::casters;

  uses('scriptlet.xml.workflow.casters.ParamCaster');
  
  /**
   * Casts given values to booleans
   *
   * @purpose  Caster
   */
  class ToBoolean extends ParamCaster {
  
    /**
     * Cast a given value
     *
     * @see     xp://scriptlet.xml.workflow.casters.ParamCaster
     * @param   array value
     * @return  array value
     */
    public function castValue($value) {
      static $map= array(
        'true'  => TRUE,
        'yes'   => TRUE,
        'on'    => TRUE,
        '1'     => TRUE,
        'false' => FALSE,
        'no'    => FALSE,
        'off'   => FALSE,
        '0'     => FALSE
      );
      
      $return= array();
      foreach ($value as $k => $v) {
        $lookup= trim(strtolower($v));
        if (!isset($map[$lookup])) return NULL; // An error occured
        
        $return[$k]= $map[$lookup];
      }

      return $return;
    }
  }
?>
