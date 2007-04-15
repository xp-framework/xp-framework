<?php
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'io.File',
    'io.FileUtil',
    'util.profiling.Timer',
    'xml.compact.CompactXmlLexer', 
    'xml.compact.CompactXmlParser'
  );
  
  $p= new ParamString();
  $input= new File($p->value(1));
  
  $t= new Timer();
  $t->start();

  $parser= new CompactXmlParser();
  $r= $parser->parse(new CompactXmlLexer(
    FileUtil::getContents($input), 
    $input->getURI()
  ));
  
  $t->stop();

  Console::writeLine(str_repeat('-', 72));
  Console::writeLine($r->getSource(INDENT_DEFAULT));
  Console::writeLine(str_repeat('-', 72));
  
  Console::writeLinef(
    'Parse %s: %.3f seconds', 
    basename($input->getUri()), 
    $t->elapsedTime()
  );
?>
