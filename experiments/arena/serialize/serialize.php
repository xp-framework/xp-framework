<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */
  require('lang.base.php');
  uses('FileSerializer', 'Date', 'util.profiling.Timer');
  
  $date= new Date();
  echo $date->toString();
  
  $t= new Timer();
  $t->start();
  $s= new FileSerializer(new File('test.ser'));
  $s->serialize($date);
  $t->stop();
  printf("Elapsed time: %.5f seconds\n", $t->elapsedTime());
  
  echo str_replace('\n', "\n", addcslashes(file_get_contents('test.ser'), "\0..\17")), "\n";
?>
