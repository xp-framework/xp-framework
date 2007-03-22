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
    'de.schlund.db.rubentest.RubentestColor'
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
  
  Console::writeLine('-------- get entity by primary key ---------------------------------------');
  $c1= RubentestColor::getByColor_id(1);
  Console::writeLine($c1->getColor_id());
  Console::writeLine($c1->getName());
  Console::writeLine($c1->getColortype());

  Console::writeLine('-------- get entity by unique index ---------------------------------------');
  $c2= RubentestColor::getByColortype('green');
  Console::writeLine($c2->getColortype());
  Console::writeLine($c2->getColor_id());
  Console::writeLine($c2->getName());

  Console::writeLine('-------- set primary key befor loading ---------------------------------------');
  $c3= RubentestColor::getByColor_id(2);
  $c3->setColor_id(5);
  $c3->setColortype('blau');
  Console::writeLine($c3->getColortype());
  Console::writeLine($c3->getColor_id());
  Console::writeLine($c3->getName());

  Console::writeLine('-------- get entity by unique index and load constraint Entities ---------------------------------------');
  $c4= RubentestColor::getByColortype('green');
  Console::writeLine(xp::stringOf($c4->getTextureColortypeList()));

  Console::writeLine('-------- get entity by primery index and load constraint Entities ---------------------------------------');
  $c5= RubentestColor::getByColor_id('3');
  Console::writeLine(xp::stringOf($c5->getTextureColortypeList()));

?>
