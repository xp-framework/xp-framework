<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */

  enum Coin {
    penny(1), nickel(5), dime(10), quarter(25);

    public function color() {
      switch ($this->ordinal) {
        case self::penny: return 'copper';
        case self::nickel: return 'nickel';
        case self::dime: case self::quarter: return 'silver';
      }
    }
  }


  // {{{ main
  echo 'Coin (', Coin::size(), " values):\n";
  foreach (Coin::values() as $coin) {
    echo '* ', $coin->name, ': ', $coin->ordinal, '¢ (', $coin->color(), ")\n";
  }
  // }}}
?>
