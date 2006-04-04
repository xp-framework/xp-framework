<?php
  require('lang.base.php');
  uses('OpcodeHandler', 'PNode', 'util.cmd.Console', 'io.File', 'io.FileUtil');

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
  
  function fetchfrom($storage, $id, $name, &$context) {
    if (!array_key_exists($id, $storage)) {
      error(E_NOTICE, 'Undefined '.$name.' '.$id.' in '.$context['__name']);
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
      // DEBUG Console::writeLine('MEMBER ', $pointer->id, '->', $var->args[1], ' := ', PNode::stringOf($value));
      $GLOBALS['objects'][$pointer->id]['members'][$var->args[1]]= $value;
    } else {
      // DEBUG Console::writeLine('VAR ', $var->args[0], ' := ', PNode::stringOf($value));
      $context['variables'][$var->args[0]]= $value;
    }
  }
  
  function except(&$throwable, &$context) {
    $context['E']= &$throwable;
  }
  
  function methodcall(&$method, &$context) {

    // Find method declaration
    $static= is_scalar($method->args[0]);
    if ($static) {
      $class= $method->args[0];
      // DEBUG Console::writeLine('INVOKE: ', $class.'::'.$method->args[1]);
    } else {
      $pointer= &value($method->args[0], $context);
      
      // Check for NPE
      if (!is_a($pointer, 'ObjectInstance')) {
        except(new NullPointerException(xp::stringOf($pointer).'->'.$method->args[1]), $context);
        return;
      }
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
    error(E_ERROR, 'Call to undefined method '.$method->toString());
  }

  function functioncall(&$function, &$context) {
    if (!isset($context['functions'][$function->args[0]])) {
      error(E_ERROR, 'Call to undefined function '.$function->toString());
      // bails
    }
    
    $callcontext= $context;
    $callcontext['variables']= array();
    $decl= &$context['functions'][$function->args[0]];
    for ($i= 0, $s= sizeof($function->args[1]); $i < $s; $i++) {
      $argumentName= $decl->args[2][$i]->args[2];
      $callcontext['variables'][$argumentName]= value($function->args[1][$i], $context);
    }

    // DEBUG var_dump($function->args[0], $callcontext['variables']);

    // - Execute
    $context['__name']= $function->args[0];
    return execute($decl->args[3], $callcontext);
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
      error(E_ERROR, 'Unknown class '.$classname);
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
    if (!$context) xp::error('value() invoked outside of context');
    
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
          return function_exists($node->args[0]) 
            ? builtincall($node, $context)
            : functioncall($node, $context)
          ;
          break;

        case 'Binary':
          switch ($node->args[2]) {
            case '<=':
              return value($node->args[0], $context) <= value($node->args[1], $context);
              break;
            
            case '==':
              return value($node->args[0], $context) == value($node->args[1], $context);
              break;
            
            case '.':
              return value($node->args[0], $context).value($node->args[1], $context);
              break;
            
            default:
              error(E_ERROR, 'Unsupported binary operator '.$node->args[2]);
              // Bails
          }
          break;

        case 'Not':
          return !value($node->args[0], $context);
          break;
        
        case 'PreInc':
          $new= value($node->args[0], $context)+ 1;
          set($node->args[0], $new, $context);
          return $new;
          break;
        
        default:
          error(E_ERROR, 'Cannot retrieve value representation of '.$node->toString());
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


  function execute($nodes, &$context) {
    
    $i= 0;
    $context['offset']= &$i;
    $context['return']= 0;
    $context['end']= sizeof($nodes);

    // DEBUG Console::writeLine(PNode::stringOf($context), '>>>');
    
    for ($i= 0, $s= $context['end']; $i < $s; $i++) {
      handle($nodes[$i], $context);
      if ($context['E']) break;
    }

    // Console::writeLine('>>> returned ', xp::stringOf($context['return']));
    
    return $context['return'];
  }
  
  function handle(&$node, &$context) {
    $id= $node->type;
    if (!isset($context['handlers'][$id])) {
      error(E_NOTICE, 'Unknown node '.PNode::stringOf($node));
      return;
    }

    // echo $context['__name'], ' *** ', $node->toString(), ' ***', "\n";
    $context['handlers'][$id]->handle(
      $context, 
      $node
    );
  }

  // {{{ handlers
  $handlers= array();
  $handlers['Assign']= &opcode('
    set($node->args[0], value($node->args[1], $context), $context);
  ');
  $handlers['PreInc']= &opcode('
    $new= value($node->args[0], $context)+ 1;
    set($node->args[0], $new, $context);
  ');
  $handlers['If']= &opcode('
    // if (condition) { if-statements } [ elseif { elseif-statements }] [ else { else-statements }]
    if (value($node->args[0], $context)) {

      // condition: true
      $block= &$node->args[1];
    } else if ($node->args[2]) {

      // condition: false, else if
      if (value($node->args[2]->args[0], $context)) {
        $block= &$node->args[2]->args[1];
      } else {
        $block= &$node->args[3];
      }
    } else if ($node->args[3]) {

      // condition: false, else
      $block= &$node->args[3];
    }

    foreach ($block as $arg) {
      handle($arg, $context);
    }
  ');
  $handlers['For']= &opcode('
    // for (init; condition; loop) { statements }
    // init
    foreach ($node->args[0] as $arg) {
      handle($arg, $context);
    }
    
    while (value($node->args[1][0], $context)) {  // condition
    
      // statements 
      foreach ($node->args[3] as $arg) {
        handle($arg, $context);
      }
      
      // loop
      handle($node->args[2][0], $context);
    }
  ');
  $handlers['Foreach']= &opcode('
    // foreach ($var as [$key =>] $val) { statements }
    var_dump($node);
    
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
  $handlers['FunctionDeclaration']= &opcode('
    $context["functions"][$node->args[1]]= $node;
  ');
  $handlers['FunctionCall']= &opcode('
    function_exists($node->args[0]) 
      ? builtincall($node, $context)
      : functioncall($node, $context)
    ;
  ');
  $handlers['MethodCall']= &opcode('
    methodcall($node, $context);
  ');
  $handlers['Try']= &opcode('
    execute($node->args[0], $context);
    $oE= &$context["E"];
    $context["E"] && handle($node->args[1], $context);
    
    // If Exception was not caught by `catch`, pass it to the
    // next catch (if available).
    // Do not pass a newly thrown exception to these catches (thus comparing
    // the ids).
    if (NULL !== $node->args[2] && $context["E"] && $oE->id === $context["E"]->id) {
      execute($node->args[2], $context);
    }
    
    // Execute finally
    if (NULL !== $node->args[2]) {
      execute($node->args[2]->args[0], $context);
    }
  ');
  $handlers['Catch']= &opcode('
    $pointer= &$context["E"];
    $class= $GLOBALS["objects"][$pointer->id]["name"];
    
    if ($node->args[0] == $class) {
      $context["variables"][$node->args[1]]= &value($context["E"], $context);
      unset($context["E"]);
      execute($node->args[2], $context);
    }
  ');
  $handlers['Throw']= &opcode('
    except(value($node->args[0], $context), $context);
  ');
  // }}}

  // {{{ main
  $nodes= unserialize(FileUtil::getContents(new File($argv[1])));
  
  $context= array();
  $context['E']= NULL;
  $context['__name']= '<main>';
  $context['handlers']= $handlers;
  array_shift($argv);
  $context['variables']= array();
  $context['variables']['$argc']= $argc;
  $context['variables']['$argv']= $argv;
  
  execute($nodes, $context);
  
  $context['E'] && error(E_ERROR, '*** Uncaught '.$context['E']->toString().' ('.$GLOBALS["objects"][$context["E"]->id]["name"].')');
  // }}}
?>
