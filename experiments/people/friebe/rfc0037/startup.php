<?php
/* This file is part of the XP framework's peoples' experiments
 *
 * $Id: startup.php 5797 2005-09-21 11:41:41Z friebe $
 */

  $startup_start= microtime(TRUE);
  require($argv[1].'/lang.base.php');
  $startup_stop= microtime(TRUE);

  $core_classes= XPClass::getClasses();
  
  $load_start= microtime(TRUE);
  uses('util.Binford', 'util.Date', 'util.Hashmap');
  $load_stop= microtime(TRUE);
  
  printf("Startup time: %.3f seconds\n", $startup_stop - $startup_start);
  foreach ($core_classes as $i => $class) {
    printf("- Core #%2d: %s\n", $i, $class->getName());
  }

  printf("Load classes: %.3f seconds\n", $load_stop - $load_start);
  foreach (array_slice(XPClass::getClasses(), sizeof($core_classes)) as $i => $class) {
    printf("- User #%2d: %s\n", $i, $class->getName());
  }
?>
