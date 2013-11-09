<?php namespace net\xp_framework\unittest\xml;

/**
 * Type factory
 */
#[@xmlmapping(factory= 'forName', pass= array('@id'))]
class IdBasedTypeFactory extends \lang\Object {
  
  /**
   * Factory method
   *
   * @param   string $id
   * @return  lang.XPClass
   * @throws  lang.IllegalArgumentException
   */
  public static function forName($id) {
    switch ($id) {
      case 'dialog': return \lang\XPClass::forName('net.xp_framework.unittest.xml.DialogType');
      case 'button': return \lang\XPClass::forName('net.xp_framework.unittest.xml.ButtonType');
      default: throw new \lang\IllegalArgumentException('Unknown attribute "'.$id.'"');
    }
  }
}
