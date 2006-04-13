<?php
  require('lang.base.php');
  uses(
    'net.xp_framework.tools.vm.Parser',
    'net.xp_framework.tools.vm.Lexer',
    'net.xp_framework.tools.vm.PNode', 
    'util.cmd.Console', 
    'io.File', 
    'io.FileUtil'
  );
  
  class compiler {
    function error($level, $message) {
      switch ($level) {
        case E_ERROR:
        case E_CORE_ERROR:
        case E_COMPILE_ERROR:
          xp::error($message);
          // Bails out
      }
      echo '*** ', $message, "\n";
    }

    function call($function, $args= array()) {
      // Can be removed as soon as XP2.jay doesn't use this anymore
    }

  }
  
  function get_next_op_number($a) {
    // Can be removed as soon as XP2.jay doesn't use this anymore
    return 1;
  }
  
  // {{{ compile
  $parser= &new Parser();
  $parser->debug= FALSE;
  $nodes= $parser->yyparse(new Lexer(file_get_contents($argv[1]), $argv[1]));
  xp::gc();
  
  isset($argv[2]) && Console::writeLine(PNode::stringOf($nodes));
  
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
