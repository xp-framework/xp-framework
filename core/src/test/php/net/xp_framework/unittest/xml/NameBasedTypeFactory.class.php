<?php namespace net\xp_framework\unittest\xml;

/**
 * Type factory
 */
#[@xmlmapping(factory= 'forName')]
class NameBasedTypeFactory extends \lang\Object {
  
  /**
   * Factory method
   *
   * @param   string $name
   * @return  lang.XPClass
   * @throws  lang.IllegalArgumentException
   */
  public static function forName($name) {
    switch ($name) {
      case 'dialog': return \lang\XPClass::forName('net.xp_framework.unittest.xml.DialogType');
      case 'button': return \lang\XPClass::forName('net.xp_framework.unittest.xml.ButtonType');
      default: throw new \lang\IllegalArgumentException('Unknown tag "'.$name.'"');
    }
  }
}
