<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */
  require('lang.base.php');
  uses('enum+xp://enum.Coin');
  
  // {{{ main
  echo 'Coin: ', Coin::size(), "\n";
  foreach (Coin::values() as $coin) {
    echo $coin->name, ': ', $coin->value, '¢ (', $coin->color(), ")\n";
  }
  // }}}
?>
