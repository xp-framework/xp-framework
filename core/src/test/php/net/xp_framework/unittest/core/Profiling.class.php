<?php namespace net\xp_framework\unittest\core;

/**
 * Profiling enumeration
 */
class Profiling extends \lang\Enum {
  public static
    $INSTANCE,
    $EXTENSION;
  
  public static
    $fixture = NULL;
  
  static function __static() {
    self::$INSTANCE= new self(0, 'INSTANCE');
    self::$EXTENSION= new self(1, 'EXTENSION');
  }

  /**
   * Returns all enum members
   *
   * @return  lang.Enum[]
   */
  public static function values() {
    return parent::membersOf(__CLASS__);
  }
}
