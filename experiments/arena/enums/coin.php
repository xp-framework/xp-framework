<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */
  require('lang.base.php');
  require('enum.php');
  enum('Coin');
  
  // Accessing all values
  echo 'Coin::values()= '; var_dump(Coin::values());
  
  // Accessing a single value
  echo 'Coin_PENNY= '; var_dump(Coin_PENNY);
  
  // Calling a function
  echo 'Coin::colorOf(Coin_DIME)= '; var_dump(Coin::colorOf(Coin_DIME));
?>
