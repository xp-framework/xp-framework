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
    'rdbms.criterion.Projections',
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
  $crit= array();
  
  $crits['count']= new Criteria();
  $crits['count']->setProjection(
    Projections::count()
  );
  
  $crits['average']= new Criteria();
  $crits['average']->setProjection(
    Projections::average("color_id")
  );
  
  $crits['max']= new Criteria();
  $crits['max']->setProjection(
    Projections::max("color_id")
  );
  
  $crits['min']= new Criteria();
  $crits['min']->setProjection(
    Projections::min("color_id")
  );
  
  $crits['sum']= new Criteria();
  $crits['sum']->setProjection(
    Projections::sum("color_id")
  );

  $crits['property']= new Criteria();
  $crits['property']->setProjection(
    Projections::property("name")
  );
  
  $crits['projectionList']= new Criteria();
  $crits['projectionList']->setProjection(
    Projections::projectionList()
    ->add(Projections::property("color_id", 'id'))
    ->add(Projections::property("name"))
  );

  $crits['plain']= new Criteria();

  foreach ($crits as $name => $crit) {
    Console::writeLine("\nlist for $name:");
    $l= Ncolor::getPeer()->doSelect($crit, 1);
    Console::writeLine(xp::stringOf($l));

    Console::writeLine(xp::stringOf("\niterator for $name:"));
    $l= Ncolor::getPeer()->iteratorFor($crit);
    Console::writeLine(xp::stringOf($l->next()));
  }

  $crit= Criteria::newInstance()->add(Restrictions::like("name", "%green"));
  Console::writeLine(xp::stringOf("\n\nwithProjection count:"));
  Console::writeLine(xp::stringOf(Ncolor::getPeer()->iteratorFor($crit->withProjection(Projections::count()))->next()->get('count')));
  Console::writeLine(xp::stringOf("\n\nwithout projection:"));
  Console::writeLine(xp::stringOf(Ncolor::getPeer()->doSelect($crit)));

?>
