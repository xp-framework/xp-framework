<?php
  require('lang.base.php');
  uses(
    'net.xp_framework.tools.vm.Parser',
    'net.xp_framework.tools.vm.Lexer',
    'util.cmd.Console', 
    'io.File', 
    'io.FileUtil'
  );
  define('MODIFIER_NATIVE', 8);   // See lang.XPClass
  
  // {{{ compile
  $parser= &new Parser();
  $parser->debug= FALSE;
  $nodes= $parser->yyparse(new Lexer(file_get_contents($argv[1]), $argv[1]));
  
  // Dump AST if specified
  isset($argv[2]) && Console::writeLine(VNode::stringOf($nodes));
  
  // Write to file
  $out= &new File($argv[1].'c');
  try(); {
    FileUtil::setContents($out, serialize($nodes));
  } if (catch('IOException', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  
  Console::writeLine('---> ', $out->getURI());
  // }}}
?>
