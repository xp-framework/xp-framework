<?php
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'net.xp_framework.tools.vm.Parser',
    'net.xp_framework.tools.vm.Lexer',
    'net.xp_framework.tools.vm.emit.php5.Php5Emitter',
    'util.cmd.Console', 
    'io.File', 
    'io.FileUtil',
    'util.log.Logger',
    'util.log.ConsoleAppender',
    'util.cmd.ParamString'
  );
  define('MODIFIER_NATIVE', 8);   // See lang.XPClass
  define('CLASSPATH', strtr('php5-emit/demo:php5-emit/skeleton:php5-emit/ports/classes', ':', PATH_SEPARATOR));
  
  // {{{ compile
  $p= new ParamString();
  if (!$p->exists(1) || !is_file($in= $p->value(1))) {
    Console::writeLine('- Could not find "'.$in.'"');
    exit(1);
  }
  
  $cat= NULL;
  if ($p->exists('debug')) {
    $cat= Logger::getInstance()->getCategory();
    $cat->addAppender(new ConsoleAppender(), $p->value('debug', 'd'));
  }
  
  $lexer= new Lexer(file_get_contents($in), $in);
  $out= new File($p->value('out', 'o', str_replace('.xp', '.php', $in)));
  
  $parser= new Parser();
  $nodes= $parser->parse($lexer);
  
  // Dump AST if specified
  $p->exists('ast') && Console::writeLine(VNode::stringOf($nodes));
  
  $emitter= new Php5Emitter();
  $emitter->setTrace($cat);
  $emitter->setFilename($in);
  $emitter->emitAll($nodes);

  if ($emitter->hasErrors()) {
    Console::writeLine('!!! Errors have occured');
    foreach ($emitter->getErrors() as $error) {
      Console::writeLine('- ', $error->toString());
    }
    exit(1);
  }
  
  FileUtil::setContents($out, $emitter->getResult());
  
  $p->exists('quiet') || Console::writeLine('---> ', $out->getURI());
  exit(0);
  // }}}
?>
