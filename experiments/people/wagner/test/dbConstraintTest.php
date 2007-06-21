<?php
/* This file is part of the XP framework
 *
 * $Id$ 
 */

  require('/home/ruben/Work/xp.private/tags/XAR_RELEASE/5.4.0/lang.base.php');
  xp::sapi('cli');

  uses(    
    'util.log.Logger',
    'util.log.ColoredConsoleAppender',
    'util.PropertyManager',
    'rdbms.ConnectionManager',
    'de.schlund.db.rubentest.Nmappoint',
    'de.schlund.db.rubentest.Mperson'
  );

  // Params
  $p= new ParamString();
  
  // Display help
  if ($p->exists('help', 'h')) {
    Console::writeLinef('...');
    exit();
  }
  
  Logger::getInstance()->getCategory()->addAppender(new ColoredConsoleAppender());
  ConnectionManager::getInstance()->register(DriverManager::getConnection('mysql://test:test@localhost/Ruben_Test_PS?autoconnect=1&log=default'));
  
  var_dump(Nmappoint::getByCoord_xCoord_y(1, 2)->getTexture());

  var_dump(Mperson::getPeer()->doSelect(new Criteria(Restrictions::equal('person_id', 3))));

?>
