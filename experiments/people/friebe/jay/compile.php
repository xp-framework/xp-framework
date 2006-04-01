<?php
  require('lang.base.php');
  require('Parser.php');
  require('Lexer.php');

  uses('OpcodeHandler', 'PNode', 'util.cmd.Console');
  
  class ObjectInstance extends Object {
    var $id= NULL;
    
    function __construct($id) {
      $this->id= $id;
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
      function handle(&$context, &$node) {
        '.$bytes.'
      }
    }');
  }
  // }}}
  
  function fetchfrom($storage, $id, $name, &$context) {
    if (!array_key_exists($id, $storage)) {
      compiler::error(E_NOTICE, 'Undefined '.$name.' '.$id.' in '.$context['__name']);
      return NULL;
    }
    
    return $storage[$id];
  }
  
  function fetch(&$var, &$context) {
    $value= fetchfrom($context['variables'], $var->args[0], 'variable', $context);
          
    // Lookup variable contents
    if ($var->args[1]['arrayoffset']) {
      return $value[$var->args[1]['arrayoffset']];
    } else {
      return $value;
    }
  }
  
  function set(&$var, $value, &$context) {
    if ('ObjectReference' == $var->type) {
      $pointer= &value($var->args[0], $context);
      Console::writeLine('MEMBER ', $pointer->id, '->', $var->args[1], ' := ', PNode::stringOf($value));
      $GLOBALS['objects'][$pointer->id]['members'][$var->args[1]]= $value;
    } else {
      // DEBUG Console::writeLine('VAR ', $var->args[0], ' := ', PNode::stringOf($value));
      $context['variables'][$var->args[0]]= $value;
    }
  }
  
  function methodcall(&$method, &$context) {

    // Find method declaration
    $static= is_scalar($method->args[0]);
    if ($static) {
      $class= $method->args[0];
      // DEBUG Console::writeLine('INVOKE: ', $class.'::'.$method->args[1]);
    } else {
      $pointer= &value($method->args[0], $context);
      $class= $GLOBALS['objects'][$pointer->id]['name'];
      // DEBUG Console::writeLine('INVOKE: ', $class.'->'.$method->args[1]);
    }

    foreach ($context['classes'][$class]->args[5] as $decl) {
      if ($decl->type != 'MethodDeclaration' || $decl->args[4] != $method->args[1]) continue;
      
      // We've found the method declaration, now:
      // - Build argument list
      $callcontext= $context;
      $callcontext['variables']= array();
      for ($i= 0, $s= sizeof($method->args[2]); $i < $s; $i++) {
        $argumentName= $decl->args[5][$i]->args[2];
        $callcontext['variables'][$argumentName]= value($method->args[2][$i], $context);
      }
      
      // DEBUG var_dump($method->args[0].'::'.$method->args[1], $callcontext['variables']);
      
      // - Execute
      $context['__name']= $class.($static ? '::' : '->').$method->args[1];
      if (!$static) $callcontext['variables']['$this']= &$pointer;
      return execute($decl->args[7], $callcontext);
    }
    
    // Undefined method
    compiler::error(E_ERROR, 'Call to undefined method '.$method->toString());
  }

  function builtincall(&$function, &$context) {
    $arguments= array();
    for ($i= 0, $s= sizeof($function->args[1]); $i < $s; $i++) {
      $arguments[]= value($function->args[1][$i], $context);
    }
    
    return call_user_func_array($function->args[0], $arguments);
  }
  
  function createobject(&$object, &$context) {
    $id= microtime();
    $classname= $object->args[0]->args[0];
    if (!isset($context['classes'][$classname])) {
      compiler::error(E_ERROR, 'Unknown class '.$classname);
    }

    // Register to object storage    
    $GLOBALS['objects'][$id]= array(
      'name'    => $classname,
      'members' => array()
    );
    $pointer= &new ObjectInstance($id); 
    
    // Call constructor if existant
    foreach ($context['classes'][$classname]->args[5] as $decl) {
      if ($decl->type != 'ConstructorDeclaration') continue;
      
      // Found a constructor, invoke it!
      $callcontext= $context;
      $callcontext['variables']= array();
      for ($i= 0, $s= sizeof($object->args[1]); $i < $s; $i++) {
        $argumentName= $decl->args[2][$i]->args[2];
        $callcontext['variables'][$argumentName]= value($object->args[1][$i], $context);
      }
      
      // - Execute, discarding return values (constructors cannot return anything!)
      $callcontext['variables']['$this']= &$pointer;
      $callcontext['__name']= 'new '.$classname;
      execute($decl->args[4], $callcontext);
    }

    // Return pointer to storage
    return $pointer;
  }
  
  function value(&$node, &$context) {
    if (is_a($node, 'PNode')) {
      switch ($node->type) {
        case 'Variable':
          return fetch($node, $context);
          break;
         
        case 'MethodCall':
          return methodcall($node, $context);
          break;

        case 'New':
          return createobject($node, $context);
          break;
        
        case 'ObjectReference':
          $pointer= &value($node->args[0], $context);

          return fetchfrom(
            $GLOBALS['objects'][$pointer->id]['members'], 
            $node->args[1], 
            'member of '.$pointer->id, 
            $context
          );
          break;

        case 'FunctionCall':
          if (function_exists($node->args[0])) {
            return builtincall($node, $context);
          }
          // TBI
          break;

        default:
          compiler::error(E_ERROR, 'Cannot retrieve value representation of '.$node->toString());
          // Bails
      }
    } else if ('"' == $node{0}) { // Double-quoted string
      $value= '';
      for ($i= 1, $s= strlen($node)- 1; $i < $s; $i++) {
        if ('\\' == $node{$i}) {
          switch ($node{$i+ 1}) {
            case 'r': $value.= "\r"; break;
            case 'n': $value.= "\n"; break;
            case 't': $value.= "\b"; break;
          }
          $i++;
        } else {
          $value.= $node{$i};
        }
      }
      return $value;
    } else if ("'" == $node{0}) { // Single-quoted string
      return substr($node, 1, -1);
    }

    return $node;
  }

  // {{{ handlers
  $handlers= array();
  $handlers['Assign']= &opcode('
    set($node->args[0], value($node->args[1], $context), $context);
  ');
  $handlers['Echo']= &opcode('
    foreach ($node->args[0] as $arg) {
      $value= value($arg, $context);
      
      if (is_scalar($value)) {
        echo $value;
      } else if (is_array($value)) {
        echo "Array";
      } else if (is_object($value)) {
        if (method_exists($value, "toString")) echo $value->toString(); else echo "Object";
      }
    }
  ');
  $handlers['Exit']= &opcode('
    if (isset($node->args[0])) {
      $context["exitcode"]= value($node->args[0], $context);
    }
    $context["offset"]= $context["end"];
  ');
  $handlers['Return']= &opcode('
    if (isset($node->args[0])) {
      $context["return"]= value($node->args[0], $context);
    }
    $context["offset"]= $context["end"];
  ');
  $handlers['ClassDeclaration']= &opcode('
    $context["classes"][$node->args[2]]= $node;
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
  
  function execute($nodes, $context) {
    
    $i= 0;
    $context['offset']= &$i;
    $context['return']= 0;
    $context['end']= sizeof($nodes);

    // DEBUG Console::writeLine(PNode::stringOf($context), '>>>');
    
    for ($i= 0, $s= $context['end']; $i < $s; $i++) {
      $id= $nodes[$i]->type;
      if (!isset($context['handlers'][$id])) {
        compiler::error(E_NOTICE, 'Unknown node '.$id);
        continue;
      }

      // DEBUG echo $context['__name'], ' *** ', $nodes[$i]->toString(), ' ***', "\n";
      $context['handlers'][$id]->handle(
        $context, 
        $nodes[$i]
      );
    }

    // Console::writeLine('>>> returned ', xp::stringOf($context['return']));
    
    return $context['return'];
  }
  
  // {{{ compile
  $parser= &new Parser();
  $parser->debug= FALSE;
  $nodes= $parser->yyparse(new AspectTokenizer(file_get_contents($argv[1]), $argv[1]));
  xp::gc();
  // }}}
  
  // {{{ execute
  $context= array();
  $context['__name']= '<main>';
  $context['handlers']= $handlers;
  array_shift($argv);
  $context['variables']= array();
  $context['variables']['$argc']= $argc;
  $context['variables']['$argv']= $argv;
  
  execute($nodes, $context);
  // }}}
?>
