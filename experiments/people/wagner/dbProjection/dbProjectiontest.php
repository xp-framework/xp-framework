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
  
  $crit= new Criteria();
  $crit->setProjection(
    Projections::projectionList()
      ->add(Projections::average("person_id"))
  );
  
  $l= RubentestPerson::getPeer()->doSelect($crit);
  var_dump($l);

?>
