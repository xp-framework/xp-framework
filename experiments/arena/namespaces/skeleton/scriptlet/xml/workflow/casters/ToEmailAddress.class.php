<?php
/* This class is part of the XP framework
 *
 * $Id: ToEmailAddress.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace scriptlet::xml::workflow::casters;

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
          $addr= peer::mail::InternetAddress::fromString($v);
        } catch (lang::FormatException $e) {
          return $e->getMessage();
        }
        
        $return[$k]= $addr;
      }

      return $return;
    }
  }
?>
