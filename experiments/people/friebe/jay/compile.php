<?php
  require('lang.base.php');
  require('Parser.php');
  require('Lexer.php');

  uses('OpcodeHandler', 'OpcodeArray', 'PNode', 'util.cmd.Console');
  
  class Variable extends Object {
    var 
      $name   = '';

    function __construct($name) {
      $this->name= $name;
    }

    function toString() {
      return 'php.Variable('.$this->__id.')@['.$this->name.']';
    }
  }

  // {{{ &lang.Object newinstance(string class, string bytes)
  //     Instance creation "expression"
  function &newinstance($class, $bytes) {
    static $i= 0;

    if (!class_exists($class)) {
      xp::error(xp::stringOf(new Error('Class "'.$class.'" does not exist')));
      // Bails
    }

    $name= $class.'·'.++$i;
    xp::registry('class.'.strtolower($name), $name);
    
    $c= $class;
    while ($c= get_parent_class($c)) {
      if ('interface' != $c) continue;
      
      // It's an interface
      eval('class '.$name.' extends Object '.$bytes);
      implements($name.'.class.php', $class);
      return new $name();
    }
    
    // It's a class
    eval('class '.$name.' extends '.$class.' '.$bytes);
    return new $name();
  }
  // }}}
  
  // {{{ &OpcodeHandler opcode(string bytes)
  //     Creates an opcode handler
  function &opcode($bytes) {
    return newinstance('OpcodeHandler', '{
      function handle(&$context, &$args) {
        '.$bytes.'
      }
    }');
  }
  // }}}

  // {{{ handlers
  $handlers= array();
  $handlers['zend_do_try']= &opcode('
    $context["E"]= NULL;
  ');
  $handlers['zend_do_begin_catch']= &opcode('
    if (!$context["E"]) {

      // Search for end catch opcode
      for ($i= $context["O"]->offset; $i < $context["O"]->size; $i++) {
        if ("zend_do_end_catch" != $context["O"]->opcodes[$i][0]) continue;
        $context["O"]->offset= $i- 1;
        return;
      }
    }

    $context["symbols"][$args[2]]= &$context["E"];
  ');
  $handlers['zend_do_end_catch']= &opcode('
    $context["E"]= NULL;
  ');
  $handlers['zend_do_throw']= &opcode('
    $exception= &$context["T"];

    // Search for catch opcode
    for ($i= $context["O"]->offset; $i < $context["O"]->size; $i++) {
      if ("zend_do_begin_catch" != $context["O"]->opcodes[$i][0]) continue;

      // Check whether exception was caught by the found opcode
      if (!is($context["O"]->opcodes[$i][1][1], $exception)) continue;

      // We have found the correct opcode
      $context["E"]= &$exception;
      $context["O"]->offset= $i- 1;
      return;
    }

    xp::error("Uncaught exception ".$exception->toString());
  ');
  $handlers['zend_do_begin_new_object']= &opcode('
    $classname= xp::reflect($args[1]);
    if (!class_exists($classname)) return;
    $context["T"]= new $classname();
  ');
  $handlers['zend_do_begin_class_member_function_call']= &opcode('
    execute($GLOBALS["opcodes"][$args[0]."::".$args[1]]);
  ');
  $handlers['zend_do_echo']= &opcode('
    echo xp::stringOf($args[0]);
  ');
  $handlers['do_assign']= &opcode('
    Console::writeLine("-> Setting ", $args[0]->name, " to ", xp::stringOf($args[1]));
    $context["symbols"][$args[0]->name]= $args[1];
  ');
  // }}}
  
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
    }

  }
  
  function get_next_op_number($a) {
    return 1;
  }
  
  function execute(&$opcodes) {
    global $handlers;

    Console::writeLine('+++ Executing ', $opcodes->hashCode());
    $context= array('O' => &$opcodes);
    for ($opcodes->offset= 0; $opcodes->offset < $opcodes->size; $opcodes->offset++) {
      $opcodes->dump($opcodes->offset);

      $id= $opcodes->opcodes[$opcodes->offset][0];
      if (!isset($handlers[$id])) {
        // DEBUG Console::writeLinef('Unknown opcode "%s"', $id);
        continue;
      }
      $handlers[$id]->handle(
        $context, 
        $opcodes->opcodes[$opcodes->offset][1]
      );

      // $opcodes->dump($opcodes->offset);
    }
    Console::writeLine('--- Done executing ', $opcodes->hashCode());
  }
  
  // {{{ compile
  $CG['package']= NULL;
  $CG['class']= NULL;
  $CG['imports']= array();
  $opcodes[NULL]= &new OpcodeArray();
  $parser= &new Parser();
  $parser->debug= FALSE;
  $parser->yyparse(new AspectTokenizer(file_get_contents($argv[1]), $argv[1]));
  xp::gc();
  // }}}
  
  // {{{ execute
  // execute($opcodes[NULL]);
  // }}}
?>
