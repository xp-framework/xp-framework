<?php
  require('lang.base.php');
  uses(
    'net.xp_framework.tools.vm.OpcodeHandler', 
    'net.xp_framework.tools.vm.PNode', 
    'net.xp_framework.tools.vm.VNode', 
    'util.cmd.Console', 
    'io.File', 
    'io.FileUtil'
  );

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
    } else if ('Variable' == $var->type) {
      // Console::writeLine('VAR ', $var->args[0], ' := ', PNode::stringOf($value));
      $context['variables'][$var->args[0]]= $value;
    } else {
      error(E_ERROR, 'Cannot assign to '.PNode::stringOf($var));
    }
  }
  
  function except(&$throwable, &$context) {
    $context['E']= &$throwable;
  }
  
  function &method($class, $type, $name, $arguments, &$context) {
        
    // DEBUG Console::writeLine('Looking for ', $class, '::', $type, '(', $name, ') ', PNode::stringOf($arguments));
    $fallback= NULL;
    foreach ($context['classes'][$class]->args[5] as $decl) {
      if (!is_a($decl, $type) || ($name && $decl->name != $name)) continue;
      
      $fallback= $decl;

      // Found a possible candidate, now compare signatures
      // DEBUG Console::writeLine(' -> candidate ', PNode::stringOf($decl));
      for ($i= 0, $s= sizeof($decl->parameters); $i < $s; $i++) {
        $decltype= $decl->parameters[$i]->args[0];
        // DEBUG Console::writeLine('    declares arg #', $i, ' as ', $decltype);

        $v= value($arguments[$i], $context);
        $argtype= gettype($v);
        // DEBUG Console::writeLine('    argument #', $i, ' is ', $argtype, ': ', PNode::stringOf($v));
        
        // Compare types XXX FIXME XXX inheritance / scalar / ...
        if ($argtype != $decltype) {
          // DEBUG Console::writeLine('    *** mismatch, continuing search');
          continue 2;
        }
      }

      // DEBUG Console::writeLine(' -> using ', PNode::stringOf($decl));
      return $decl;
    }

    // DEBUG Console::writeLine(' -> using ', PNode::stringOf($fallback));
    return $fallback;
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

    if ($decl= &method($class, 'MethodDeclarationNode', $method->args[1], $method->args[2], $context)) {
      
      // We've found the method declaration, now:
      // - Build argument list
      $callcontext= $context;
      $callcontext['variables']= array();
      for ($i= 0, $s= sizeof($method->args[2]); $i < $s; $i++) {
        $argumentName= $decl->parameters[$i]->args[2];
        $callcontext['variables'][$argumentName]= value($method->args[2][$i], $context);
      }
      
      // DEBUG var_dump($method->args[0].'::'.$method->args[1], $callcontext['variables']);
      
      // - Execute
      $context['__name']= $class.($static ? '::' : '->').$method->args[1];
      if (!$static) $callcontext['variables']['$this']= &$pointer;
      return execute($decl->statements, $callcontext);
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
      $argumentName= $decl->parameters[$i]->args[2];
      $callcontext['variables'][$argumentName]= value($function->args[1][$i], $context);
    }

    // DEBUG var_dump($function->args[0], $callcontext['variables']);

    // - Execute
    $context['__name']= $function->args[0];
    return execute($decl->statements, $callcontext);
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
    $classname= $object->class->args[0];
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
    if ($decl= &method($classname, 'ConstructorDeclarationNode', NULL, $object->arguments, $context)) {
      
      // Found a constructor, invoke it!
      $callcontext= $context;
      $callcontext['variables']= array();
      for ($i= 0, $s= sizeof($object->arguments); $i < $s; $i++) {
        $argumentName= $decl->parameters[$i]->args[2];
        $callcontext['variables'][$argumentName]= value($object->arguments[$i], $context);
      }
      
      // - Execute, discarding return values (constructors cannot return anything!)
      $callcontext['variables']['$this']= &$pointer;
      $callcontext['__name']= 'new '.$classname;
      execute($decl->statements, $callcontext);
    }

    // Return pointer to storage
    return $pointer;
  }
  
  function overloaded($op, &$l, &$r, &$out, &$context) {
    if (!is_a($l, 'ObjectInstance')) return FALSE;    // Short-cuircuit

    $class= $GLOBALS['objects'][$l->id]['name'];
    if ($decl= &method($class, 'OperatorDeclarationNode', $op, array(&$l, &$r), $context)) {

      // Found overloaded operator
      $callcontext= $context;
      $callcontext['variables'][$decl->parameters[0]->args[2]]= &$l;
      $callcontext['variables'][$decl->parameters[1]->args[2]]= &$r;

      // DEBUG var_dump($class.'::operator'.$op, $callcontext['variables']);

      $callcontext['__name']= $class.'::operator'.$op;
      $out= execute($decl->statements, $callcontext);
      return TRUE;
    }

    // Couldn't find an overloaded operator, fall through
    return FALSE;
  }
  
  function binaryop($op, &$l, &$r, &$context) {
    switch ($op) {
      case '<':
        return value($l, $context) < value($r, $context);
        break;

      case '<=':
        return value($l, $context) <= value($r, $context);
        break;

      case '>':
        return value($l, $context) > value($r, $context);
        break;

      case '>=':
        return value($l, $context) >= value($r, $context);
        break;

      case '==':
        return value($l, $context) == value($r, $context);
        break;

      case '===':
        return value($l, $context) === value($r, $context);
        break;

      case '!=':
        return value($l, $context) != value($r, $context);
        break;

      case '!==':
        return value($l, $context) !== value($r, $context);
        break;

      // Overloadable operators
      case '.':
        $left= value($l, $context);
        $right= value($r, $context);

        return overloaded('.', $left, $right, $v, $context) ? $v : $left.$right;
        break;

      case '+':
        $left= value($l, $context);
        $right= value($r, $context);

        return overloaded('+', $left, $right, $v, $context) ? $v : $left + $right;
        break;

      case '-':
        $left= value($l, $context);
        $right= value($r, $context);

        return overloaded('-', $left, $right, $v, $context) ? $v : $left - $right;
        break;

      case '*':
        $left= value($l, $context);
        $right= value($r, $context);

        return overloaded('*', $left, $right, $v, $context) ? $v : $left * $right;
        break;

      case '/':
        $left= value($l, $context);
        $right= value($r, $context);

        return overloaded('/', $left, $right, $v, $context) ? $v : $left / $right;
        break;

      case '%':
        $left= value($l, $context);
        $right= value($r, $context);

        return overloaded('%', $left, $right, $v, $context) ? $v : $left % $right;
        break;

      default:
        error(E_ERROR, 'Unsupported binary operator '.$node->args[2]);
        // Bails
    }
  }
  
  function value(&$node, &$context) {
    if (!$context) xp::error('value() invoked outside of context');
    
    if (is_a($node, 'PNode') || is_a($node, 'VNode')) {
      $id= is_a($node, 'PNode') ? strtolower($node->type) : substr(get_class($node), 0, -4);
      
      switch ($id) {
        case 'variable':
          return fetch($node, $context);
          break;
         
        case 'methodcall':
          return methodcall($node, $context);
          break;

        case 'new':
          return createobject($node, $context);
          break;
        
        case 'objectreference':
          $pointer= &value($node->args[0], $context);

          return fetchfrom(
            $GLOBALS['objects'][$pointer->id]['members'], 
            $node->args[1], 
            'member of '.$pointer->id, 
            $context
          );
          break;

        case 'functioncall':
          return function_exists($node->args[0]) 
            ? builtincall($node, $context)
            : functioncall($node, $context)
          ;
          break;

        case 'binary':
          return binaryop($node->operator, $node->left, $node->right, $context);
          break;

        case 'not':
          return !value($node->expression, $context);
          break;
        
        case 'preinc':  // ++$i
          $new= value($node->args[0], $context)+ 1;
          set($node->args[0], $new, $context);
          return $new;
          break;

        case 'postinc': // $i++
          $new= value($node->args[0], $context);
          set($node->args[0], $new+ 1, $context);
          return $new;
          break;

        case 'predec':  // --$i
          $new= value($node->args[0], $context)- 1;
          set($node->args[0], $new, $context);
          return $new;
          break;

        case 'postdec': // $i--
          $new= value($node->args[0], $context);
          set($node->args[0], $new- 1, $context);
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
    $id= is_a($node, 'PNode') ? strtolower($node->type) : substr(get_class($node), 0, -4);

    if (!isset($context['handlers'][$id])) {
      error(E_NOTICE, 'Unknown '.$id.' node '.PNode::stringOf($node));
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
  $handlers['assign']= &opcode('
    set($node->variable, value($node->expression, $context), $context);
  ');
  $handlers['preinc']= &opcode('
    $new= value($node->args[0], $context)+ 1;
    set($node->args[0], $new, $context);
  ');
  $handlers['if']= &opcode('
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
  $handlers['for']= &opcode('
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
  $handlers['while']= &opcode('
    // while (condition) { statements }
    while (value($node->args[0], $context)) {  // condition

      // statements 
      foreach ($node->args[1] as $arg) {
        handle($arg, $context);
      }
    }
  ');
  $handlers['binaryassign']= &opcode('
    set(
      $node->variable, 
      binaryop($node->operator, $node->variable, $node->expression, $context), 
      $context
    );
  ');
  $handlers['echo']= &opcode('
    foreach ($node->args as $arg) {
      $value= value($arg, $context);
      
      if (is_scalar($value)) {
        echo $value;
      } else if (is_array($value)) {
        echo "Array";
      } else if (is_a($value, "ObjectInstance")) {
        echo "Object (".$GLOBALS["objects"][$value->id]["name"].")";
      }
    }
  ');
  $handlers['exit']= &opcode('
    if (isset($node->args[0])) {
      $context["exitcode"]= value($node->args[0], $context);
    }
    $context["offset"]= $context["end"];
  ');
  $handlers['return']= &opcode('
    if (isset($node->value)) {
      $context["return"]= value($node->value, $context);
    }
    $context["offset"]= $context["end"];
  ');
  $handlers['classdeclaration']= &opcode('
    $context["classes"][$node->args[2]]= $node;
  ');
  $handlers['functiondeclaration']= &opcode('
    $context["functions"][$node->name]= $node;
  ');
  $handlers['functioncall']= &opcode('
    function_exists($node->args[0]) 
      ? builtincall($node, $context)
      : functioncall($node, $context)
    ;
  ');
  $handlers['methodcall']= &opcode('
    methodcall($node, $context);
  ');
  $handlers['try']= &opcode('
    execute($node->args[0], $context);
    $oE= &$context["E"];
    $context["E"] && handle($node->args[1], $context);
    
    // If Exception was not caught by `catch`, pass it to the
    // next catch (if available).
    // Do not pass a newly thrown exception to these catches (thus comparing
    // the ids).
    if (NULL !== $node->args[1]->args[3] && $context["E"] && $oE->id === $context["E"]->id) {
      execute($node->args[1]->args[3], $context);
    }
    
    // Execute finally
    if (NULL !== $node->args[2]) {
      execute($node->args[2]->args[0], $context);
    }
  ');
  $handlers['catch']= &opcode('
    $pointer= &$context["E"];
    $class= $GLOBALS["objects"][$pointer->id]["name"];
    
    if ($node->args[0] == $class) {
      $context["variables"][$node->args[1]]= &value($context["E"], $context);
      unset($context["E"]);
      execute($node->args[2], $context);
    }
  ');
  $handlers['throw']= &opcode('
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
