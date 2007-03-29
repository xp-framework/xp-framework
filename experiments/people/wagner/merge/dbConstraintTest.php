<?php
/* This file is part of the XP framework
 *
 * $Id$ 
 */

  require('lang.base.php');
  xp::sapi('cli');

  uses(    
    'util.log.Logger',
    'util.log.ColoredConsoleAppender',
    'util.PropertyManager',
    'rdbms.ConnectionManager',
    'de.schlund.db.rubentest.RubentestPerson'
  );

  // Params
  $p= new ParamString();
  
  // Display help
  if ($p->exists('help', 'h')) {
    Console::writeLinef('...');
    exit();
  }
  
  Logger::getInstance()->getCategory()->addAppender(new ColoredConsoleAppender());
  ConnectionManager::getInstance()->register(DriverManager::getConnection('mysql://test:test@localhost/?autoconnect=1&log=default'));
  
  var_dump(RubentestPerson::getByPerson_id(1)->getToilettePerson());

?>
