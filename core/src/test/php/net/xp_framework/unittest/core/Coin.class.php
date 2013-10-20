<?php namespace net\xp_framework\unittest\core;

/**
 * Coin enumeration
 * 
 * @purpose  Demo
 */
class Coin extends \lang\Enum {
  public static
    $penny,
    $nickel,
    $dime,
    $quarter;
  
  static function __static() {
    self::$penny= new self(1, 'penny');
    self::$nickel= new self(2, 'nickel');
    self::$dime= new self(10, 'dime');
    self::$quarter= new self(25, 'quarter');
  }

  /**
   * Return this coin's value in cent
   *
   * @return  int
   */
  public function value() {
    return $this->ordinal;
  }

  /**
   * Return this coin's color
   *
   * @return  string
   */
  public function color() {
    switch ($this) {
      case self::$penny: return 'copper';
      case self::$nickel: return 'nickel';
      case self::$dime: case self::$quarter: return 'silver';
    }
  }
}
