<?php
/* This class is part of the XP framework
 *
 * $Id: Coin.class.php 10908 2007-07-31 12:06:15Z friebe $ 
 */

  namespace net::xp_framework::unittest::core;

  ::uses('lang.Enum');
  
  /**
   * Coin enumeration
   * 
   * @purpose  Demo
   */
  class Coin extends lang::Enum {
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
     * Returns all enum members
     *
     * @return  lang.Enum[]
     */
    public static function values() {
      return parent::membersOf(__CLASS__);
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
?>
