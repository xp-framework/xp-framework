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
  
  Console::writeLine('');
  Console::writeLine('-------- 1) get entity by primary key ---------------------------------------');
  Console::writeLine(' ==> get color by Id 1');
  $c1= Ncolor::getByColor_id(1);
  Console::write(' ==> get color id: ');
  Console::writeLine($c1->getColor_id());
  Console::write(' ==> get name: ');
  Console::writeLine($c1->getName());
  Console::write(' ==> get color type: ');
  $c1->getColortype();

  Console::writeLine('');
  Console::writeLine('-------- 2) set primary key befor loading ---------------------------------------');
  Console::writeLine(' ==> get color by id 3');
  $c3= Ncolor::getByColor_id(2);
  Console::writeLine(' ==> set color to id 5');
  $c3->setColor_id(5);
  Console::write(' ==> get color id: ');
  Console::writeLine($c3->getColor_id());
  Console::write(' ==> get color type: ');
  $c3->getColortype();
  Console::write(' ==> get color name: ');
  Console::writeLine($c3->getName());

  Console::writeLine('');
  Console::writeLine('-------- 3) get entity by primery index and serialize it to string an to XML ---------------------------------------');
  $c6= Ncolor::getByColor_id('3');
  Console::writeLine(' ==> -- XML --');
  Console::writeLine(Node::fromObject($c6, 'color')->getSource(INDENT_DEFAULT));
  Console::writeLine(' ==> -- PHP serialize --');
  Console::writeLine(addcslashes(serialize($c6), "\0..\17"));

  Console::writeLine('');
  Console::writeLine('-------- 4) chaining ---------------------------------------');
  Console::writeLine(' ==> get Color by ID 3');
  $c7= Ncolor::getByColor_id('3');
  Console::writeLine(' ==> get TextureList by Color');
  $textures= $c7->getNtextureColorList();
  Console::writeLine(' ==> found '.count($textures).' textures');
  foreach ($textures as $e) {
    Console::writeLine(' ==> iterate');
    Console::write('    ==> get texture name: ');
    Console::writeLine($e->getName());
    Console::writeLine('    ==> get Mappoints');
    $mpps= $e->getNmappointTextureList();
  }

?>
