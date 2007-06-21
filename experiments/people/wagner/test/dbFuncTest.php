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
    'util.Date',
    'rdbms.ConnectionManager',
    'rdbms.criterion.Restrictions',
    'rdbms.SQLFunctions',
    'de.schlund.db.rubensqtest.Nmappoint'
//    'de.schlund.db.rubentest.Nmappoint'
  );

  // Params
  $p= new ParamString();
  
  // Display help
  if ($p->exists('help', 'h')) {
    Console::writeLinef('...');
    exit();
  }
  
  Logger::getInstance()->getCategory()->addAppender(new ColoredConsoleAppender());
  ConnectionManager::getInstance()->register(DriverManager::getConnection('sqlite://%2Fhome%2Fruben%2Fhtdocs%2Fadmin%2FSQLite%2Fdb%2FRuben_Test_PS.db/Ruben_Test_PS?autoconnect=1&log=default'));
//  ConnectionManager::getInstance()->register(DriverManager::getConnection('mysql://test:test@localhost/?autoconnect=1&log=default'));

  $crits= array();
do {
  $crits[]= Criteria::newInstance()->setProjection(SQLFunctions::day(SQLFunctions::getdate()), 'dayTest');
  $crits[]= Criteria::newInstance()->setProjection(SQLFunctions::ascii("a"), 'asciiTest');
  $crits[]= Criteria::newInstance()->setProjection(SQLFunctions::char('97'), 'charTest');
  $crits[]= Criteria::newInstance()->setProjection(SQLFunctions::len("aaaaaaa"), 'lentest');
  $crits[]= Criteria::newInstance()->setProjection(SQLFunctions::reverse("abcdefg"), 'reverseTest');
  $crits[]= Criteria::newInstance()->setProjection(SQLFunctions::space('4'), 'spaceTest');
  $crits[]= Criteria::newInstance()->setProjection(SQLFunctions::concat('aa', SQLFunctions::str(SQLFunctions::getdate()), 'cc'), 'concatTest');
  $crits[]= Criteria::newInstance()->setProjection(SQLFunctions::str(SQLFunctions::getdate()), 'getdateTest');
  $crits[]= Criteria::newInstance()->setProjection(SQLFunctions::str(SQLFunctions::dateadd('month', '-4', SQLFunctions::getdate())), 'dateaddTest');
//  $crits[]= Criteria::newInstance()->setProjection(SQLFunctions::datediff('second', SQLFunctions::dateadd('day', '-4', SQLFunctions::getdate()), SQLFunctions::getdate()), 'datediffTest');
  $crits[]= Criteria::newInstance()->setProjection(SQLFunctions::datename('hour', SQLFunctions::getdate()), 'datenameTest');
  $crits[]= Criteria::newInstance()->setProjection(SQLFunctions::datepart('hour', new Date()), 'datenameTest');
  $crits[]= Criteria::newInstance()->setProjection(SQLFunctions::abs(-6), 'absTest');
  $crits[]= Criteria::newInstance()->setProjection(SQLFunctions::cot(45), 'cotTest');
  $crits[]= Criteria::newInstance()->setProjection(SQLFunctions::pi(), 'piTest');
  $crits[]= Criteria::newInstance()->setProjection(SQLFunctions::acos(SQLFunctions::cos(0.125)), 'cosAcosTest');
  $crits[]= Criteria::newInstance()->setProjection(SQLFunctions::asin(SQLFunctions::sin(0.125)), 'sinAsinTest');
  $crits[]= Criteria::newInstance()->setProjection(SQLFunctions::atan(SQLFunctions::tan(0.125)), 'tanAtanTest');
  $crits[]= Criteria::newInstance()->setProjection(SQLFunctions::atan(SQLFunctions::tan(0.125), 0), 'tanAtan2Test');
  $crits[]= Criteria::newInstance()->setProjection(SQLFunctions::degrees(SQLFunctions::pi()), 'degreesTest');
  $crits[]= Criteria::newInstance()->setProjection(SQLFunctions::radians(SQLFunctions::degrees(90)), 'radiansTest');
  $crits[]= Criteria::newInstance()->setProjection(SQLFunctions::ceil(5.1), 'ceilTest');
  $crits[]= Criteria::newInstance()->setProjection(SQLFunctions::floor(5.7), 'floorTest');
  $crits[]= Criteria::newInstance()->setProjection(SQLFunctions::exp(SQLFunctions::log(1)), 'expTest');
  $crits[]= Criteria::newInstance()->setProjection(SQLFunctions::log(SQLFunctions::exp(1)), 'logTest');
  $crits[]= Criteria::newInstance()->setProjection(SQLFunctions::log10(SQLFunctions::power(10, 5)), 'powerTest');
  $crits[]= Criteria::newInstance()->setProjection(SQLFunctions::power(10, SQLFunctions::log10(5)), 'log10Test');
  $crits[]= Criteria::newInstance()->setProjection(SQLFunctions::rand(), 'randTest');
  $crits[]= Criteria::newInstance()->setProjection(Projections::ProjectionList()->add(SQLFunctions::round(1.50), 'roundtest1')->add(SQLFunctions::round(1.49), 'roundtest2')->add(SQLFunctions::round(1.49, 1), 'roundtest3'));
  $crits[]= Criteria::newInstance()->setProjection(Projections::ProjectionList()->add(SQLFunctions::sign(-7), 'signTest1')->add(SQLFunctions::sign(0), 'signTest2')->add(SQLFunctions::sign(4), 'signTest3'));
  $crits[]= Criteria::newInstance()->setProjection(SQLFunctions::soundex("kawabanga"), 'soundexTest');
  $crits[]= Criteria::newInstance()->setProjection(SQLFunctions::cast("345", 'decimal'), 'datatypesTest');
  $crits[]= Criteria::newInstance()->setProjection(SQLFunctions::cast(Nmappoint::column("texture_id"), 'char'), 'datatypesTest');
  $crits[]= Criteria::newInstance()->add(Restrictions::equal("texture_id", SQLFunctions::ceil(SQLFunctions::asin(SQLFunctions::sin(0.125)))));
  $crits[]= Criteria::newInstance()->add(Restrictions::equal(Nmappoint::column("texture_id"), SQLFunctions::ceil(SQLFunctions::asin(SQLFunctions::sin(0.125)))));
  $crits[]= Criteria::newInstance()->setProjection(SQLFunctions::locate("foobar", "bar"));
  $crits[]= Criteria::newInstance()->setProjection(SQLFunctions::locate(NULL, "bar"));
  $crits[]= Criteria::newInstance()->setProjection(SQLFunctions::locate("foobarfoo", "foo", 4));
  $crits[]= Criteria::newInstance()->setProjection(SQLFunctions::substring("foobarfoo", 2, 4));
} while (false);

  foreach ($crits as $name => $crit) {
    Console::writeline("\n$name:");
    Console::writeline(xp::stringOf(Nmappoint::getPeer()->iteratorFor($crit)->next()));
  }

?>
