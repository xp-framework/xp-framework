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
  $p= new ParamString();
  $in= $p->value(1);
  
  // "-" means compile STDIN
  if ('-' == $in) {
    $c= '';
    while ($buf= fgets(STDIN, 1024)) {
      $c.= $buf;
    }
    $lexer= new Lexer($c, '<standard input>');
  } else {
    $lexer= new Lexer(file_get_contents($in), $in);
  }
  
  $parser= new Parser();
  $nodes= $parser->parse($lexer);
  
  // Dump AST
  Console::writeLine(VNode::stringOf($nodes));
  // }}}
?>
