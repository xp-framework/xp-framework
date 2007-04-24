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
    'de.schlund.db.rubentest.Nmappoint'
  );

  // Params
  $p= new ParamString();
  
  // Display help
  if ($p->exists('help', 'h')) {
    Console::writeLinef('...');
    exit();
  }
  
  $crit= array();
  Logger::getInstance()->getCategory()->addAppender(new ColoredConsoleAppender());
  ConnectionManager::getInstance()->register(DriverManager::getConnection('mysql://test:test@localhost/?autoconnect=1&log=default'));

  $crits[]= Criteria::newInstance()->setProjection(Projections::Property(SQLFunctions::day(SQLFunctions::getdate()), 'dayTest'));
  $crits[]= Criteria::newInstance()->setProjection(Projections::Property(SQLFunctions::ascii('"a"'), 'asciiTest'));
  $crits[]= Criteria::newInstance()->setProjection(Projections::Property(SQLFunctions::char('60'), 'charTest'));
  $crits[]= Criteria::newInstance()->setProjection(Projections::Property(SQLFunctions::len('"aaaaaaa"'), 'lentest'));
  $crits[]= Criteria::newInstance()->setProjection(Projections::Property(SQLFunctions::reverse('"abcdefg"'), 'reverseTest'));
  $crits[]= Criteria::newInstance()->setProjection(Projections::Property(SQLFunctions::space('4'), 'spaceTest'));
  $crits[]= Criteria::newInstance()->setProjection(Projections::Property(SQLFunctions::concat('"aa"', SQLFunctions::str(SQLFunctions::getdate()), '"cc"'), 'concatTest'));
  $crits[]= Criteria::newInstance()->setProjection(Projections::Property(SQLFunctions::str(SQLFunctions::getdate()), 'getdateTest'));
  $crits[]= Criteria::newInstance()->setProjection(Projections::Property(SQLFunctions::str(SQLFunctions::dateadd('month', '-4', SQLFunctions::getdate())), 'dateaddTest'));
  $crits[]= Criteria::newInstance()->setProjection(Projections::Property(SQLFunctions::datediff('second', SQLFunctions::dateadd('day', '-4', SQLFunctions::getdate()), SQLFunctions::getdate())), 'datediffTest');
  $crits[]= Criteria::newInstance()->setProjection(Projections::Property(SQLFunctions::datename('hour', SQLFunctions::getdate()), 'datenameTest'));
  $crits[]= Criteria::newInstance()->setProjection(Projections::Property(SQLFunctions::datepart('hour', new Date()), 'datenameTest'));
  $crits[]= Criteria::newInstance()->setProjection(Projections::Property(SQLFunctions::abs(-6), 'absTest'));
  $crits[]= Criteria::newInstance()->setProjection(Projections::Property(SQLFunctions::acos(0), 'acosTest'));
  $crits[]= Criteria::newInstance()->setProjection(Projections::Property(SQLFunctions::asin(0.5), 'asinTest'));
  $crits[]= Criteria::newInstance()->setProjection(Projections::Property(SQLFunctions::atan(2), 'atanTest'));
  $crits[]= Criteria::newInstance()->setProjection(Projections::Property(SQLFunctions::atan(2, 0), 'atan2Test'));
  $crits[]= Criteria::newInstance()->setProjection(Projections::Property(SQLFunctions::ceil(5.1), 'ceilTest'));
  $crits[]= Criteria::newInstance()->setProjection(Projections::Property(SQLFunctions::cos(45), 'cosTest'));
  $crits[]= Criteria::newInstance()->setProjection(Projections::Property(SQLFunctions::cot(45), 'cotTest'));
  $crits[]= Criteria::newInstance()->setProjection(Projections::Property(SQLFunctions::degrees(SQLFunctions::pi()), 'degreesTest'));
  $crits[]= Criteria::newInstance()->setProjection(Projections::Property(SQLFunctions::exp(1), 'expTest'));
  $crits[]= Criteria::newInstance()->setProjection(Projections::Property(SQLFunctions::floor(5.7), 'floorTest'));
  $crits[]= Criteria::newInstance()->setProjection(Projections::Property(SQLFunctions::log(SQLFunctions::exp(1)), 'logTest'));
  $crits[]= Criteria::newInstance()->setProjection(Projections::Property(SQLFunctions::log10(SQLFunctions::power(10, 5)), 'log10Test'));
  $crits[]= Criteria::newInstance()->setProjection(Projections::Property(SQLFunctions::power(10, 5)), 'powerTest');
  $crits[]= Criteria::newInstance()->setProjection(Projections::Property(SQLFunctions::pi()), 'piTest');
  $crits[]= Criteria::newInstance()->setProjection(Projections::Property(SQLFunctions::radians(90), 'radiansTest'));
  $crits[]= Criteria::newInstance()->setProjection(Projections::Property(SQLFunctions::rand(), 'randTest'));
  $crits[]= Criteria::newInstance()->setProjection(Projections::ProjectionList()->add(Projections::Property(SQLFunctions::round(1.50), 'roundtest1'))->add(Projections::Property(SQLFunctions::round(1.49), 'roundtest2'))->add(Projections::Property(SQLFunctions::round(1.49, 1), 'roundtest3')));
  $crits[]= Criteria::newInstance()->setProjection(Projections::ProjectionList()->add(Projections::Property(SQLFunctions::sign(-7), 'signTest1'))->add(Projections::Property(SQLFunctions::sign(0), 'signTest2'))->add(Projections::Property(SQLFunctions::sign(4), 'signTest3')));
  $crits[]= Criteria::newInstance()->setProjection(Projections::Property(SQLFunctions::sin(180), 'sinTest'));
  $crits[]= Criteria::newInstance()->setProjection(Projections::Property(SQLFunctions::tan(45), 'tanTest'));
  $crits[]= Criteria::newInstance()->setProjection(Projections::Property(SQLFunctions::soundex("'ogawoga'"), 'soundexTest'));

  foreach ($crits as $name => $crit) {
    echo "\n$name:\n";
    var_dump(Nmappoint::getPeer()->iteratorFor($crit)->next());
  }

?>
