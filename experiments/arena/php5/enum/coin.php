<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */

  enum Coin {
    penny(1), nickel(5), dime(10), quarter(25)
  }

  function colorOf(Coin $coin) {
    switch ($coin->ordinal) {
      case Coin::penny: return 'copper';
      case Coin::nickel: return 'nickel';
      case Coin::dime: case Coin::quarter: return 'silver';
    }
  }

  // {{{ main
  echo 'Coin (', Coin::size(), " values):\n";
  foreach (Coin::values() as $coin) {
    echo '* ', $coin->name, ': ', $coin->ordinal, '¢ (', colorOf($coin), ")\n";
  }
  // }}}
?>
