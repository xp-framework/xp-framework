<?php
/* This class is part of the XP framework
 *
 * $Id: ToURL.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace scriptlet::xml::workflow::casters;

  uses('scriptlet.xml.workflow.casters.ParamCaster', 'peer.URL');
  
  /**
   * Casts given values to peer.URL objects
   *
   * @see      xp://peer.URL
   * @purpose  Caster
   */
  class ToURL extends ParamCaster {
  
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
        $return[$k]= new peer::URL($v);
      }

      return $return;
    }
  }
?>
