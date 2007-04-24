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
    'xml.Node',
    'rdbms.ConnectionManager',
    'rdbms.criterion.Restrictions',
    'de.schlund.db.rubentest.Ncolor'
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
  
  $c1= Ncolor::getPeer()->iteratorFor(new Criteria(
    Restrictions::free('name = "lightgreen"')
  ));
  
  var_dump($c1->next());

?>
