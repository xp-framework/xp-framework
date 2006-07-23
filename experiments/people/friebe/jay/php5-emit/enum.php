<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$ 
 */
  
  require('__xp__.php');
  
  // {{{ Original
  // enum Coin {
  //   penny(1), nickel(5), dime(10), quarter(25);
  //
  //   public string color() {
  //     switch ($this) {
  //       case penny: return 'copper';
  //       case nickel: return 'nickel';
  //       case dime: case quarter: return 'silver';
  //     }
  //   }
  // }
  //
  // foreach (Coin::values() as $value) {
  //   echo $coin->name, ': ', $coin->value, '¢ (', $coin->color(), ")\n";
  // }
  // }}}

  // {{{ Generated version
  class Coin extends xp·lang·Object {
    public static $penny;
    public static $nickel;
    public static $dime;
    public static $quarter;
    
    private static $values= array();
    public $name;
    public $value;
    
    private function __construct($name, $value) {
      $this->name= $name;
      $this->value= $value;
    }
    
    public static function __static() {
      self::$values[]= self::$penny= new Coin('penny', 1);
      self::$values[]= self::$nickel= new Coin('nickel', 5);
      self::$values[]= self::$dime= new Coin('dime', 10);
      self::$values[]= self::$quarter= new Coin('quarter', 25);
    }
    
    public static function values() {
      return self::$values;
    }

    public function color() {
      switch ($this) {
        case self::$penny: return 'copper';
        case self::$nickel: return 'nickel';
        case self::$dime: case self::$quarter: return 'silver';
      }
    }
  } Coin::__static();

  foreach (Coin::values() as $coin) {
    echo $coin->name, ': ', $coin->value, '¢ (', $coin->color(), ")\n";
  }
  // }}}
?>
