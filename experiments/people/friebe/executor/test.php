<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('OpcodeHandler', 'OpcodeArray', 'util.Date');

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
      function handle(&$context, $args) {
        '.$bytes.'
      }
    }');
  }
  // }}}

  // {{{ handlers
  $handlers= array();
  $handlers['OP_NEW']= &opcode('
    $classname= xp::reflect($args[0]);
    $context["T"]= &new $classname();
  ');
  $handlers['OP_ASSIGN']= &opcode('
    $context["symbols"][$args[0]]= &$context["T"];
  ');
  $handlers['OP_PRINTLN']= &opcode('
    Console::writeLine(xp::stringOf($context["symbols"][$args[0]]));
  ');
  $handlers['OP_INVOKE']= &opcode('
    $context["T"]= call_user_func_array(
      array($context["symbols"][$args[0]], $args[1]),
      $args[2]
    );
  ');
  $handlers['OP_TRY']= &opcode('
    $context["E"]= NULL;
  ');
  $handlers['OP_CATCH']= &opcode('
    if (!$context["E"]) {

      // Search for end catch opcode
      for ($i= $context["O"]->offset; $i < $context["O"]->size; $i++) {
        if ("OP_END_CATCH" != $context["O"]->opcodes[$i][0]) continue;
        $context["O"]->offset= $i- 1;
        return;
      }
    }

    $context["symbols"][$args[1]]= &$context["E"];
  ');
  $handlers['OP_END_CATCH']= &opcode('
    $context["E"]= NULL;
  ');
  $handlers['OP_THROW']= &opcode('
    $classname= xp::reflect($args[0]);
    $exception= &new $classname();

    // Search for catch opcode
    for ($i= $context["O"]->offset; $i < $context["O"]->size; $i++) {
      if ("OP_CATCH" != $context["O"]->opcodes[$i][0]) continue;

      // Check whether exception was caught by the found opcode
      if (!is($context["O"]->opcodes[$i][1][0], $exception)) continue;

      // We have found the correct opcode
      $context["E"]= &$exception;
      $context["O"]->offset= $i- 1;
      return;
    }

    xp::error("Uncaught exception ".$exception->toString());
  ');
  $handlers['OP_EXIT']= &opcode('
    exit(isset($args[0]) ? $args[0] : 0);
  ');
  // }}}
  
  // {{{ opcodes
  // Translated from:
  //
  // <code>
  //   $date= new xp·util·Date();
  //   $string= $date->toString();
  //
  //   try {
  //     throw new xp·lang·IllegalArgumentException();
  //     exit();  // Should not be executed
  //   } catch (xp·lang·IllegalArgumentException $e) {
  //     println $e;
  //   }
  //
  //   println $string;
  // </code>
  
  $opcodes= &new OpcodeArray();
  $opcodes->add('OP_NEW', array('util.Date'));
  $opcodes->add('OP_ASSIGN', array('date'));
  $opcodes->add('OP_INVOKE', array('date', 'toString', array()));
  $opcodes->add('OP_ASSIGN', array('string'));
  $opcodes->add('OP_TRY', array());
  $opcodes->add('OP_THROW', array('lang.IllegalArgumentException'));
  $opcodes->add('OP_EXIT', array());  // Should not be executed
  $opcodes->add('OP_CATCH', array('lang.IllegalArgumentException', 'e'));
  $opcodes->add('OP_PRINTLN', array('e'));
  $opcodes->add('OP_END_CATCH', array());
  $opcodes->add('OP_PRINTLN', array('string'));
  // }}}
  
  // {{{ execute
  $context= array('O' => &$opcodes);
  for ($opcodes->offset= 0; $opcodes->offset < $opcodes->size; $opcodes->offset++) {
    $opcodes->dump($opcodes->offset);

    $handlers[$opcodes->opcodes[$opcodes->offset][0]]->handle(
      $context, 
      $opcodes->opcodes[$opcodes->offset][1]
    );
  }
  // }}}
?>
