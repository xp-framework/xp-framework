<?php
  require('lang.base.php');
  uses('rdbms.DriverManager', 'util.cmd.Console');

  Console::writeLine('Working with ', Console::$out->toString());
  
  $c= DriverManager::getConnection('mysql://scriptlet:.sybCx333@php3.de/test?autoconnect=1');
  Console::writeLine(xp::stringOf($c->query('select tasks, requirements from job where job_id= 1')->next()));
?>
