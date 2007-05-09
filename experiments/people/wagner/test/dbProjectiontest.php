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
  
  $crits['count']= Criteria::newInstance()->setProjection(
    Projections::count()
  );
  
  $crits['count1']= Criteria::newInstance()->setProjection(
    Projections::count(Ncolor::column("color_id"))
  );
  
  $crits['count2']= Criteria::newInstance()->setProjection(
    Projections::count('*', "counting all")
  );
  
  $crits['count3']= Criteria::newInstance()->setProjection(
    Projections::count(Ncolor::column("color_id"), "counting all")
  );
  
  $crits['average']= Criteria::newInstance()->setProjection(
    Projections::average(Ncolor::column("color_id"))
  );
  
  $crits['max']= Criteria::newInstance()->setProjection(
    Projections::max(Ncolor::column("color_id"))
  );
  
  $crits['min']= Criteria::newInstance()->setProjection(
    Projections::min(Ncolor::column("color_id"))
  );
  
  $crits['sum']= Criteria::newInstance()->setProjection(
    Projections::sum(Ncolor::column("color_id"))
  );

  $crits['property']= Criteria::newInstance()->setProjection(
    Projections::property(Ncolor::column("name"))
  );
  
  $crits['column']= Criteria::newInstance()->setProjection(
    Ncolor::column("name")
  );
  
  $crits['projectionList']= Criteria::newInstance()->setProjection(
    Projections::projectionList()
    ->add(Projections::property(Ncolor::column("color_id"), 'id'))
    ->add(Projections::property(Ncolor::column("name")))
  );

  $crits['columnlist']= Criteria::newInstance()->setProjection(
    Projections::projectionList()
    ->add(Ncolor::column("color_id"), 'id')
    ->add(Ncolor::column("name"))
  );

  $crits['plain']= new Criteria();

  foreach ($crits as $name => $crit) {
    Console::writeLine("\nlist for $name:");
    Console::writeLine(xp::stringOf(Ncolor::getPeer()->doSelect($crit, 1)));

    Console::writeLine(xp::stringOf("\niterator for $name:"));
    Console::writeLine(xp::stringOf(Ncolor::getPeer()->iteratorFor($crit)->next()));
  }

  $crit= Criteria::newInstance()->add(Restrictions::like(Ncolor::column("name"), "%green"));
  Console::writeLine(xp::stringOf("\n\nwithProjection count:"));
  Console::writeLine(xp::stringOf(Ncolor::getPeer()->iteratorFor($crit->withProjection(Projections::count()))->next()->get('count')));
  Console::writeLine(xp::stringOf("\n\nwithout projection:"));
  Console::writeLine(xp::stringOf(Ncolor::getPeer()->doSelect($crit)));

?>
