<?php
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'io.File',
    'io.FileUtil',
    'xml.compact.CompactXmlLexer', 
    'xml.compact.CompactXmlParser'
  );
  
  $p= new ParamString();
  $input= new File($p->value(1));
  
  $parser= new CompactXmlParser();
  $r= $parser->parse(new CompactXmlLexer(
    FileUtil::getContents($input), 
    $input->getURI()
  ));
  
  echo $r->getSource(INDENT_DEFAULT);
?>
