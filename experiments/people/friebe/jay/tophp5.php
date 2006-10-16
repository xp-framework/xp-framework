<?php
  require('lang.base.php');
  uses(
    'net.xp_framework.tools.vm.Parser',
    'net.xp_framework.tools.vm.Lexer',
    'net.xp_framework.tools.vm.emit.php5.Php5Emitter',
    'util.cmd.Console', 
    'io.File', 
    'io.FileUtil',
    'util.cmd.ParamString'
  );
  define('MODIFIER_NATIVE', 8);   // See lang.XPClass
  define('CLASSPATH', strtr('php5-emit/skeleton/', '/', DIRECTORY_SEPARATOR));
  
  // {{{ compile
  $p= &new ParamString();
  if (!$p->exists(1) || !is_file($in= $p->value(1))) {
    Console::writeLine('- Could not find "'.$in.'"');
    exit(1);
  }
  
  $lexer= &new Lexer(file_get_contents($in), $in);
  $out= &new File($p->value('out', 'o', str_replace('.xp', '.php5', $in)));
  
  $parser= &new Parser();
  try(); {
    $nodes= $parser->parse($lexer);
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  
  // Dump AST if specified
  $p->exists('ast') && Console::writeLine(VNode::stringOf($nodes));
  
  $emitter= &new Php5Emitter();
  $emitter->emitAll($nodes);

  if ($emitter->hasErrors()) {
    Console::writeLine('!!! Errors have occured');
    foreach ($emitter->getErrors() as $error) {
      Console::writeLine('- ', $error->toString());
    }
    exit(1);
  }
  
  try(); {
    FileUtil::setContents($out, $emitter->getResult());
  } if (catch('IOException', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  
  $p->exists('quiet') || Console::writeLine('---> ', $out->getURI());
  exit(0);
  // }}}
?>
