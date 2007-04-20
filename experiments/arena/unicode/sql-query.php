<?php
  require('lang.base.php');
  xp::sapi('cli');
  uses('rdbms.DriverManager');

  // {{{ main
  $p= new ParamString();
  $c= DriverManager::getConnection(rtrim($p->value(1), '/').'/?autoconnect=1');
  Console::writeLine('---> Console: ', Console::$out);
  Console::writeLine('---> Connection: ', $c);
  
  if ($p->exists(2)) {
    $value= $p->value(2);
    Console::writeLine('Writing ', $value);
    $c->update('unicode set realname= %s', $value);
  }
  
  Console::writeLine($c->select('* from unicode'));
  // }}}
?>
