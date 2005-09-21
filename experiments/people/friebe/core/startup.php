<?php
/* This file is part of the XP framework's peoples' experiments
 *
 * $Id$
 */

  function _microtime() {
    list($usec, $sec) = explode(' ', microtime()); 
    return (float)$usec + (float)$sec;
  }

  $startup_start= _microtime();
  require($argv[1].'/lang.base.php');
  $startup_stop= _microtime();

  $core_classes= XPClass::getClasses();
  
  $load_start= _microtime();
  uses('util.Binford', 'util.Date', 'util.Hashmap');
  $load_stop= _microtime();
  
  printf("Startup time: %.3f seconds\n", $startup_stop - $startup_start);
  foreach ($core_classes as $i => $class) {
    printf("- Core #%2d: %s\n", $i, $class->getName());
  }

  printf("Load classes: %.3f seconds\n", $load_stop - $load_start);
  foreach (array_slice(XPClass::getClasses(), sizeof($core_classes)) as $i => $class) {
    printf("- User #%2d: %s\n", $i, $class->getName());
  }
?>
