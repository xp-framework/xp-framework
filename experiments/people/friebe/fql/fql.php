<?php
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'FQLParser',
    'FQLLexer',
    'util.profiling.Timer'
  );
  
  // {{{ compile
  $p= new ParamString();
  $in= $p->value(1);
  
  // "-" means compile STDIN
  $t= new Timer();
  $t->start();
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
  $iterator= $parser->yyparse($lexer);
  $t->stop();
  Console::writeLinef('%.3f seconds', $t->elapsedTime());
  
  if ($parser->hasErrors()) {
    Console::writeLine('!!! Errors have occured');
    foreach ($parser->getErrors() as $error) {
      Console::writeLine('- ', $error->toString());
    }
    exit(1);
  }
  
  while ($iterator->hasNext()) {
    Console::writeLine('- ', xp::stringOf($iterator->next()));
  }
  // }}}
?>
