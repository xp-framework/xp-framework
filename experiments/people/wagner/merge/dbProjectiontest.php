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
  ConnectionManager::getInstance()->register(DriverManager::getConnection('mysql://test:test@localhost/?autoconnect=1&log=default'));
  
  $crits['count']= new Criteria();
  $crits['count']->setProjection(
    Projections::count()
  );
  
  $crits['average']= new Criteria();
  $crits['average']->setProjection(
    Projections::average("person_id")
  );
  
  $crits['max']= new Criteria();
  $crits['max']->setProjection(
    Projections::max("person_id")
  );
  
  $crits['min']= new Criteria();
  $crits['min']->setProjection(
    Projections::min("person_id")
  );
  
  $crits['sum']= new Criteria();
  $crits['sum']->setProjection(
    Projections::sum("person_id")
  );

  $crits['property']= new Criteria();
  $crits['property']->setProjection(
    Projections::property("name")
  );
  
  $crits['projectionList']= new Criteria();
  $crits['projectionList']->setProjection(
    Projections::projectionList()
    ->add(Projections::property("person_id", 'id'))
    ->add(Projections::property("name"))
  );

  $crits['plain']= new Criteria();

  foreach ($crits as $name => $crit) {
    echo "\n$name:\n";
    $l= Mperson::getPeer()->doSelect($crit, 1);
    var_dump($l);
  }

  foreach ($crits as $name => $crit) {
    echo "\n$name:\n";
    $l= Mperson::getPeer()->iteratorFor($crit);
    var_dump($l->next());
  }


  $crit= Criteria::newInstance()->add(Restrictions::equal("name", "Mary"));
  var_dump(Mperson::getPeer()->iteratorFor($crit->withProjection(Projections::count()))->next()->get('count'));
  var_dump(Mperson::getPeer()->doSelect($crit));

?>
