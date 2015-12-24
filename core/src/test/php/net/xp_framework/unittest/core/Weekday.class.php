<?php namespace net\xp_framework\unittest\core;

/**
 * Weekday enumeration
 */
class Weekday extends \lang\Enum {
  public static $MON, $TUE, $WED, $THU, $FRI, $SAT, $SUN;

  static function __static() {
    self::$MON= new self(1, 'MON');
    self::$TUE= new self(2, 'TUE');
    self::$WED= new self(3, 'WED');
    self::$THU= new self(4, 'THU');
    self::$FRI= new self(5, 'FRI');
    self::$SAT= new self(6, 'SAT');
    self::$SUN= new self(7, 'SUN');
  }

  /**
   * Returns all members for the called enum class
   *
   * @return  self[]
   */
  public static function values() {
    return self::membersOf(__CLASS__);
  }
}
