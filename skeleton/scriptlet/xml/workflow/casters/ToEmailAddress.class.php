<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('scriptlet.xml.workflow.casters.ParamCaster', 'peer.mail.InternetAddress');
  
  /**
   * Casts given values to peer.mail.InternetAddress objects
   *
   * @purpose  Caster
   */
  class ToEmailAddress extends ParamCaster {
  
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
          $addr= InternetAddress::fromString($v);
        } catch (FormatException $e) {
          return $e->getMessage();
        }
        
        $return[$k]= $addr;
      }

      return $return;
    }
  }
?>
