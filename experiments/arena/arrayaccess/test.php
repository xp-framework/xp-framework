<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */
  require('lang.base.php');
  uses('ArrayList');

  // {{{ main
  $a= new ArrayList();
  $a[]= 1;
  $a[1]= 2;
  $a[2]= 3;
  $a[]= 4;
  $a[4]= 5;
  var_dump(isset($a[0]));
  unset($a[0]);
  
  echo $a->toString(), "\n";
  var_dump(isset($a[0]));
  // }}}
?>
