<?php
  require('lang.base.php');
  uses(
    'net.xp_framework.tools.vm.Parser',
    'net.xp_framework.tools.vm.Lexer',
    'util.cmd.Console', 
    'io.File', 
    'io.FileUtil',
    'util.cmd.ParamString'
  );
  define('MODIFIER_NATIVE', 8);   // See lang.XPClass
  
  // {{{ compile
  $p= &new ParamString();
  $in= $p->value(1);
  
  // "-" means compile STDIN
  if ('-' == $in) {
    $c= '';
    while ($buf= fgets(STDIN, 1024)) {
      $c.= $buf;
    }
    $lexer= &new Lexer($c, '<standard input>');
    $out= NULL;
  } else {
    $lexer= &new Lexer(file_get_contents($in), $in);
    $out= &new File($in.'c');
  }
  
  $parser= &new Parser($lexer);
  $nodes= $parser->yyparse($lexer);
  
  // Dump AST if specified
  $p->exists('ast') && Console::writeLine(VNode::stringOf($nodes));
  
  // Write to file
  if (!$out) {
    Console::writeLine('---> Done');
    exit(0);
  }
  
  try(); {
    FileUtil::setContents($out, serialize($nodes));
  } if (catch('IOException', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  
  Console::writeLine('---> ', $out->getURI());
  // }}}
?>
