<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */

  enum Coin {
    penny(1), nickel(5), dime(10), quarter(25);

    protected static $color= array(
      self::penny   => 'copper',
      self::nickel  => 'nickel',
      self::dime    => 'silver',
      self::quarter => 'silver'
    );

    public function color() {
      return self::$color[$this->value];
    }
    
    public function __toString() {
      return $this->name.': '.$this->value.'¢ ('.$this->color().')';
    }
  }


  // {{{ main
  echo 'Coin (', Coin::size(), " values):\n";
  foreach (Coin::values() as $coin) {
    echo '* ', $coin, "\n";
  }
  // }}}
?>
