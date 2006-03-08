<?php
  require('lang.base.php');
  require('Parser.php');
  require('Lexer.php');

  uses('OpcodeHandler', 'OpcodeArray', 'util.cmd.Console');

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
    echo $args[0];
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
      $c= array('compiler', $function);
      if (is_callable($c)) {
        call_user_func_array($c, $args);
      } else {
        $GLOBALS['opcodes'][$GLOBALS['CG']['function']]->add($function, $args);
        // DEBUG echo 'C: ', $function, '(', implode(', ', array_map(array('xp', 'stringOf'), $args)), ")\n";
      }
    }

    function register($name, $value) {
      // DEBUG echo 'R: ', $name, '= ', xp::stringOf($value), "\n";
    }
    
    function resolveClass($name) {
      if ('self' == $name) return $GLOBALS['CG']['class'];
      if (NULL === $GLOBALS['CG']['package'] || strstr($name, '~')) return $name;
      return $GLOBALS['CG']['package'].'~'.$name;
    }

    function modifierNames($m) {
      $names= array();
      if ($m & MODIFIER_ABSTRACT) $names[]= 'abstract';
      if ($m & MODIFIER_FINAL) $names[]= 'final';
      switch ($m & (MODIFIER_PUBLIC | MODIFIER_PROTECTED | MODIFIER_PRIVATE)) {
        case MODIFIER_PRIVATE: $names[]= 'private'; break;
        case MODIFIER_PROTECTED: $names[]= 'protected'; break;
        case MODIFIER_PUBLIC: $names[]= 'public'; break;
        default: // Nothing
      }
      if ($m & MODIFIER_STATIC) $names[]= 'static';
      return implode(' ', $names);
    }
    
    function zend_do_ticks() { }
    function handle_interactive() { }
    function reset_doc_comment() { }
    function zend_do_extended_info() { }
    function zend_do_early_binding() { }

    // {{{ packages
    function zend_do_begin_package_declaration($name) {
      $GLOBALS['CG']['package']= $name;
    }

    function zend_do_end_package_declaration($name) {
      $GLOBALS['CG']['package']= NULL;
    }
    
    function zend_do_import($qualified, $alias) {
      if (NULL === $alias) {
        $alias= substr($qualified, strrpos($qualified, '~')+ 1);
      }

      echo '>>> ', $qualified, ' => ', $alias, "\n";
      $GLOBALS['CG']['imports'][$qualified]= $alias;
    }
    // }}}

    // {{{ classes
    function zend_do_begin_class_declaration($modifiers, $class, $extends) {
      $name= compiler::resolveClass($class);
      printf(
        ">>> Declaring %s class %s (extends %s)\n",
        compiler::modifierNames($modifiers),
        $name,
        $extends ? compiler::resolveClass($extends) : '(none)'
      );
      $GLOBALS['CG']['class']= $name;
      $GLOBALS['CG']['class_type']= 'class';
    }
    
    function zend_do_fetch_class($class, &$out) {
      $out= compiler::resolveClass($class);
    }

    function zend_do_end_class_declaration($modifiers, $class) {
      $GLOBALS['CG']['class']= NULL;
      $GLOBALS['CG']['class_type']= NULL;
    }
    // }}}

    // {{{ interfaces
    function zend_do_begin_interface_declaration($modifiers, $interface) {
      $name= compiler::resolveClass($interface);
      printf(
        ">>> Declaring %s interface %s\n",
        compiler::modifierNames($modifiers),
        $name
      );
      $GLOBALS['CG']['class']= $name;
      $GLOBALS['CG']['class_type']= 'interface';
    }

    function zend_do_end_interface_declaration($modifiers, $interface) {
      $GLOBALS['CG']['class']= NULL;
      $GLOBALS['CG']['class_type']= NULL;
    }
    
    function zend_do_implements_interface($interface) {
      printf(">>> %s implements %s\n", $GLOBALS['CG']['class'], $interface);
    }
    // }}}
    
    // {{{ enumerations
    function zend_do_begin_enum_declaration($enum) {
      $name= compiler::resolveClass($enum);
      printf(
        ">>> Declaring enum %s\n",
        $name
      );
      $GLOBALS['CG']['class']= $name;
      $GLOBALS['CG']['class_type']= 'enum';
    }
    
    function zend_do_end_enum_declaration() {
      $GLOBALS['CG']['class']= NULL;
      $GLOBALS['CG']['class_type']= NULL;
    }
    
    function zend_do_add_enum_member($name, $value) {
      printf(
        ">>> Declaring enum %s::%s (initial %s)\n",
        $GLOBALS['CG']['class'],
        $name,
        xp::stringOf($value)
      );
    }
    // }}}
    
    // {{{ constants
    function zend_do_declare_class_constant($name, $initial) {
      printf(
        ">>> Declaring %s::%s (initial %s)\n",
        $GLOBALS['CG']['class'],
        $name,
        xp::stringOf($initial)
      );
    }
    // }}}

    // {{{ properties
    function zend_do_declare_property($name, $initial, $modifiers) {
      printf(
        ">>> Declaring %s %s::%s (initial %s)\n",
        compiler::modifierNames($modifiers),
        $GLOBALS['CG']['class'],
        $name,
        xp::stringOf($initial)
      );
    }
    // }}}
    
    // {{{ methods
    function zend_do_begin_function_declaration($type, $name, $method, $ref, $modifiers) {
      $qualified= ($method ? $GLOBALS['CG']['class'].'::' : '').$name;
      printf(
        ">>> Declaring %s %s %s%s\n",
        compiler::modifierNames($modifiers),
        $type,
        $ref ? '&' : '',
        $qualified
      );
      $GLOBALS['CG']['function']= $qualified;
      $GLOBALS['opcodes'][$qualified]= &new OpcodeArray();
    }

    function zend_do_end_function_declaration($type) {
      $GLOBALS['CG']['function']= NULL;
    }

    function zend_do_throws($exception) {
      printf(
        ">>> %s throws %s\n",
        ($GLOBALS['CG']['class'] ? $GLOBALS['CG']['class'].'::' : '').$GLOBALS['CG']['function'],
        $exception
      );
    }
    
    function zend_do_abstract_method($function, $modifiers, $abstract) {
      if ('interface' == $GLOBALS['CG']['class_type']) return;

      if (!$abstract and $modifiers & MODIFIER_ABSTRACT) {
        $message= 'declared abstract but has method body';
      } else if ($abstract and !($modifiers & MODIFIER_ABSTRACT)) {
        $message= 'has no method body and must therefore be abstract';
      } else {
        return;
      }
      compiler::error(E_COMPILE_ERROR, ($GLOBALS['CG']['class'] ? $GLOBALS['CG']['class'].'::' : '').$function.' '.$message);
    }
    // }}}
    
    // {{{ annotations
    function zend_do_annotation($name, $value) {
      printf(
        ">>> Annotation %s = %s\n",
        $name,
        xp::stringOf($value)
      );
    }

    function zend_do_annotation_define($key, $value) {
      printf(
        ">>> Annotations %s = %s\n",
        $key,
        xp::stringOf($value)
      );
    }
    // }}}
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
  execute($opcodes[NULL]);
  // }}}
?>
