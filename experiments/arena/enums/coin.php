<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */
  require('enum.php');
  enum('Coin');
  
  // Accessing all values
  echo 'Coin::values()= '; var_dump(Coin::values());
  
  // Accessing a single value
  echo 'Coin_PENNY= '; var_dump(Coin_PENNY);
?>
