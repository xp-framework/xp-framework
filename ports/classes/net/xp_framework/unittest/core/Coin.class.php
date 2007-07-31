<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.Enum');
  
  /**
   * Coin enumeration
   * 
   * @purpose  Demo
   */
  class Coin extends Enum {
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
