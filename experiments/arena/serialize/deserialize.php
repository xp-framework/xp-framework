<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */
  require('lang.base.php');
  uses('FileDeserializer', 'util.profiling.Timer');
  
  $t= new Timer();
  $t->start();
  $s= new FileDeserializer(new File('test.ser'));
  $date= $s->deserialize();
  $t->stop();
  printf("Elapsed time: %.5f seconds\n", $t->elapsedTime());
  
  echo 'Deserialized: ', $date->toString(), "\n";
?>
