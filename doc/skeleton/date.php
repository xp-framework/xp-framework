<?php
/* Demo der Date-Klasse
 *
 * $Id$
 */
 
  require('lang.base.php');
  uses('util.cmd.ParamString', 'util.Date');
  
  $p= new ParamString($argv);  
  $d=($p->exists('date') ? $p->value('date') : date('Y-m-d'));
  
  $date= new Date();
  $date->fromString($d);
  var_dump($d, $date);
?>
