<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'util.PropertyManager',
    'rdbms.ConnectionManager'
  );

  // {{{ main
  $param= new ParamString();
  if ($param->count < 5) {
    Console::writeLinef('%s <username> <password> <realname> <email>',
      $param->value(0)
    );
    exit(0);
  }
  
  PropertyManager::getInstance()->configure(dirname(__FILE__).'/../etc');

  ConnectionManager::getInstance()->configure(PropertyManager::getInstance()->getProperties('database'));
  ConnectionManager::getInstance()->getByHost('pxl', 0)->insert('
    into author (
      username,
      password,
      realname,
      email
    ) values (
      %s,
      %s,
      %s,
      %s
    )
    ',
    $param->value(1),
    md5($param->value(2)),
    $param->value(3),
    $param->value(4)
  );
  
  Console::writeLine('===> User %s inserted.', $param->value(1));
?>
