<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Type factory
   *
   */
  #[@xmlmapping(factory= 'forName')]
  class NameBasedTypeFactory extends Object {
    
    /**
     * Factory method
     *
     * @param   string name
     * @return  lang.XPClass
     * @throws  lang.IllegalArgumentException
     */
    public static function forName($name) {
      switch ($name) {
        case 'dialog': return XPClass::forName('net.xp_framework.unittest.xml.DialogType');
        case 'button': return XPClass::forName('net.xp_framework.unittest.xml.ButtonType');
        default: throw new IllegalArgumentException('Unknown tag "'.$name.'"');
      }
    }
  }
?>
