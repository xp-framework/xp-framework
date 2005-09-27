<?php
  require('lang.base.php');
  uses('util.Date', 'info.binford6100.Date', 'de.thekid.List', 'xml.soap.SOAPClient');
  
  var_dump(xp::registry());
  echo 'xp::stringOf(new Date()) = ', xp::stringOf(new Date()), "\n";
  echo 'xp::stringOf(new info.binford6100.Date()) = ', xp::stringOf(new info·binford6100·Date()), "\n";
  echo 'xp::stringOf(new de.thekid.List()) = ', xp::stringOf(new de·thekid·List()), "\n";

  try(); {  
    $class= &XPClass::forName('de.thekid.util.Comparator');
  } if (catch('ClassNotFoundException', $e)) {
    $e->printStackTrace();
    exit();
  }
  echo 'XPClass::forName(\'de.thekid.util.Comparator\')->getName() = ', $class->getName(), "\n";
?>
