<?php
  require('lang.base.php');
  uses('util.Date', 'fq+xp://info.binford6100.Date', 'fq+xp://de.thekid.List');
  
  var_dump(xp::registry());
  echo 'xp::stringOf(new Date()) = ', xp::stringOf(new Date()), "\n";
  echo 'xp::stringOf(new info.binford6100.Date()) = ', xp::stringOf(new info·binford6100·Date()), "\n";
  echo 'xp::stringOf(new de.thekid.List()) = ', xp::stringOf(new de·thekid·List()), "\n";
  
  // XPClass::forName() is broken ATM
  exit;
  $class= &XPClass::forName('de.thekid.util.Comparator');
  echo 'XPClass::forName(\'de.thekid.util.Comparator\')->getName() = ', $class->getName(), "\n";
?>
