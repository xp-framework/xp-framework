<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */
  require('lang.base.php');
  require('enum.php');
  enum('Suit');
  
  // Accessing all values
  echo 'Suit::values()= '; var_dump(Suit::values());
  
  // Accessing a single value
  echo 'Suit_CLUBS= '; var_dump(Suit_CLUBS);
?>
