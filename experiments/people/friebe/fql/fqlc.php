<?php
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'FQLParser',
    'FQLLexer'
  );
  
  // {{{ compile
  $p= new ParamString();
  $in= $p->value(1);
  
  // "-" means compile STDIN
  if ('-' == $in) {
    $c= '';
    while ($buf= fgets(STDIN, 1024)) {
      $c.= $buf;
    }
    $lexer= new FQLLexer($c, '<standard input>');
  } else {
    $lexer= new FQLLexer(file_get_contents($in), $in);
  }
  
  $parser= new FQLParser($lexer);
  $nodes= $parser->yyparse($lexer);
  
  if ($parser->hasErrors()) {
    Console::writeLine('!!! Errors have occured');
    foreach ($parser->getErrors() as $error) {
      Console::writeLine('- ', $error->toString());
    }
    exit(1);
  }
  
  Console::writeLine(xp::stringOf($nodes));
  // }}}
?>
