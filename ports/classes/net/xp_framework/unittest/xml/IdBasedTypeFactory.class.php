<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Type factory
   *
   */
  #[@xmlmapping(factory= 'forName', pass= array('@id'))]
  class IdBasedTypeFactory extends Object {
    
    /**
     * Factory method
     *
     * @param   string id
     * @return  lang.XPClass
     * @throws  lang.IllegalArgumentException
     */
    public static function forName($id) {
      switch ($id) {
        case 'dialog': return XPClass::forName('net.xp_framework.unittest.xml.DialogType');
        case 'button': return XPClass::forName('net.xp_framework.unittest.xml.ButtonType');
        default: throw new IllegalArgumentException('Unknown attribute "'.$id.'"');
      }
    }
  }
?>
