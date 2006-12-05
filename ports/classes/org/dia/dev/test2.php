<?php
  require('lang.base.php');
  uses(
    'util.cmd.Console',
    'io.File',
    'xml.Tree',
    'xml.XPath',
    'xml.NodeIterator'
  );

  $Tree= &Tree::fromFile(new File('test-read.dia'));
  $Xpath= &new XPath($Tree);
  $Xpath->registerNamespace('dia', 'http://www.lysator.liu.se/~alla/dia/');

  // test xpath queries
  /**
  //dia:object[@type="UML - Class"] : all UML Class objects

  //dia:diagramdata/dia:attribute[@name="background"]/dia:color/@val

  //dia:composite[@type="paper"]/dia:attribute[@name="name"]/dia:string/text()

  //dia:object[not(starts-with(text(@type), "UML"))]

  //dia:attribute[text() | @val] ???

  **/
  try (); {
    $Xpathobj= &$Xpath->query('//dia:attribute[text()!=""]/text() or //dia:attribute/@val');
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  Console::writeLine(xp::stringOf($Xpathobj));

  exit(0);

  $Iter= &new NodeIterator($Xpathobj->nodeset);
  //Console::writeLine(xp::stringOf($Xpathobj));
  Console::writeLine(xp::typeOf($Iter->_nodes[0]));
  if (is('dom.domnode', $Iter->_nodes[0])) {
    Console::writeLine('is domnode...');
  }

  while ($Iter->hasNext()) {
    $domnode= &$Iter->next();
    Console::writeLine('Domnode: '.xp::stringOf($domnode));
    $domdoc= &$domnode->owner_document();
    $Tree= &Tree::fromString($domdoc->dump_node($domnode));
    //Console::WriteLine(method_exists($domnode, 'dump_node'));
    //Console::writeLine(xp::stringOf(get_class_methods('domnode')));
    Console::writeLine($Tree->getSource(INDENT_DEFAULT));
  }






?>
