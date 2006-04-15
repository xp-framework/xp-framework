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
    $value= fetchfrom($context['variables'], $var->name, 'variable', $context);
          
    // Lookup variable contents
    if ($var->offset) {
      return $value[$var->offset];
    } else {
      return $value;
    }
  }
  
  function set(&$var, $value, &$context) {
    if (is_a($var, 'ObjectReferenceNode')) {
      $pointer= &value($var->class, $context);
      // DEBUG Console::writeLine('MEMBER ', $pointer->id, '->', $var->member, ' := ', PNode::stringOf($value));
      $GLOBALS['objects'][$pointer->id]['members'][$var->member]= $value;
    } else if (is_a($var, 'VariableNode')) {
      // DEBUG onsole::writeLine('VAR ', $var->name, ' := ', PNode::stringOf($value));
      $context['variables'][$var->name]= $value;
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
    foreach ($context['classes'][$class]->statements as $decl) {
      if (!is_a($decl, $type) || ($name && $decl->name != $name)) continue;
      
      $fallback= $decl;

      // Found a possible candidate, now compare signatures
      // DEBUG Console::writeLine(' -> candidate ', PNode::stringOf($decl));
      for ($i= 0, $s= sizeof($decl->parameters); $i < $s; $i++) {
        $decltype= $decl->parameters[$i]->type;
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
  
  function callcontext(&$decl, &$arguments, &$context) {
    $callcontext= $context;
    $callcontext['variables']= array();
    for ($i= 0, $s= sizeof($arguments); $i < $s; $i++) {
      $callcontext['variables'][$decl->parameters[$i]->name]= value($arguments[$i], $context);
    }
    return $callcontext;
  }
  
  function methodcall(&$method, &$context) {

    // Find method declaration
    $static= is_scalar($method->class);
    if ($static) {
      $class= $method->class;
      // DEBUG Console::writeLine('INVOKE: ', $class.'::'.$method->name);
    } else {
      $pointer= &value($method->class, $context);
      
      // Check for NPE
      if (!is_a($pointer, 'ObjectInstance')) {
        except(new NullPointerException(xp::stringOf($pointer).'->'.$method->name), $context);
        return;
      }
      $class= $GLOBALS['objects'][$pointer->id]['name'];
      // DEBUG Console::writeLine('INVOKE: ', $class.'->'.$method->name);
    }

    if ($decl= &method($class, 'MethodDeclarationNode', $method->name, $method->arguments, $context)) {
      
      // We've found the method declaration, now:
      // - Build argument list
      $callcontext= callcontext($decl, $method->arguments, $context);
      
      // - Execute
      $context['__name']= $class.($static ? '::' : '->').$method->name;
      if (!$static) $callcontext['variables']['$this']= &$pointer;
      
      $return= execute($decl->statements, $callcontext);
      
      // $buf->append(' ')->append('World');
      if (NULL === $context['E'] && NULL !== $method->chain) {
        foreach ($method->chain as $reference) {
          $reference->class= $return;
          $return= value($reference, $context);

          if ($context['E']) break;
        }
      }
      
      return $return;
    }
    
    // Undefined method
    error(E_ERROR, 'Call to undefined method '.$method->toString());
  }

  function functioncall(&$function, &$context) {
    if (!isset($context['functions'][$function->name])) {
      error(E_ERROR, 'Call to undefined function '.$function->toString());
      // bails
    }
    
    // - Execute
    $context['__name']= $function->name;
    $decl= &$context['functions'][$function->name];
    
    return execute($decl->statements, callcontext(
      $decl,
      $function->arguments,
      $context
    ));
  }

  function builtincall(&$function, &$context) {
    $arguments= array();
    for ($i= 0, $s= sizeof($function->arguments); $i < $s; $i++) {
      $arguments[]= value($function->arguments[$i], $context);
    }
    
    return call_user_func_array($function->name, $arguments);
  }
  
  function createobject(&$object, &$context) {
    static $anonymous= 0;

    $id= microtime();
    $classname= $object->class->args[0];
    if (!isset($context['classes'][$classname])) {
      error(E_ERROR, 'Unknown class '.$classname);
    }
    
    // Handle anonymous class creation
    // FIXME: Use inheritance
    if ($object->instanciation->declaration) {
      $classname.= '$'.$anonymous++;
      $context['classes'][$classname]= $context['classes'][$classname];
      $context['classes'][$classname]->statements= $object->instanciation->declaration;
    }

    // Register to object storage    
    $GLOBALS['objects'][$id]= array(
      'name'    => $classname,
      'members' => array()
    );
    $pointer= &new ObjectInstance($id); 
    
    // Call constructor if existant
    if ($decl= &method($classname, 'ConstructorDeclarationNode', NULL, $object->instanciation->arguments, $context)) {
      
      // Found a constructor, invoke it!
      $callcontext= callcontext($decl, $object->instanciation->arguments, $context);
      
      // - Execute, discarding return values (constructors cannot return anything!)
      $callcontext['variables']['$this']= &$pointer;
      $callcontext['__name']= 'new '.$classname;
      execute($decl->statements, $callcontext);
    }

    if ($object->instanciation->chain) {
      foreach ($object->instanciation->chain as $reference) {
        $reference->class= $pointer;
        $pointer= value($reference, $context);

        if ($context['E']) break;
      }
    }

    // Return pointer to storage
    return $pointer;
  }
  
  function overloaded($op, &$l, &$r, &$out, &$context) {
    if (!is_a($l, 'ObjectInstance')) return FALSE;    // Short-cuircuit

    $class= $GLOBALS['objects'][$l->id]['name'];
    $args= array(&$l, &$r);
    if ($decl= &method($class, 'OperatorDeclarationNode', $op, $args, $context)) {

      // Found overloaded operator
      $callcontext= callcontext($decl, $args, $context);

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
        error(E_ERROR, 'Unsupported binary operator '.$op);
        // Bails
    }
  }
  
  function &value(&$node, &$context) {
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
          $pointer= &value($node->class, $context);

          return fetchfrom(
            $GLOBALS['objects'][$pointer->id]['members'], 
            $node->member, 
            'member of '.$pointer->id, 
            $context
          );
          break;
        
        case 'ternary':
          return (value($node->condition, $context) 
            ? value($node->expression, $context) 
            : value($node->conditional, $context)
          );
          break;

        case 'functioncall':
          return function_exists($node->name) 
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
          $new= value($node->expression, $context)+ 1;
          set($node->expression, $new, $context);
          return $new;
          break;

        case 'postinc': // $i++
          $new= value($node->expression, $context);
          set($node->expression, $new+ 1, $context);
          return $new;
          break;

        case 'predec':  // --$i
          $new= value($node->expression, $context)- 1;
          set($node->expression, $new, $context);
          return $new;
          break;

        case 'postdec': // $i--
          $new= value($node->expression, $context);
          set($node->expression, $new- 1, $context);
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
    $new= value($node->expression, $context)+ 1;
    set($node->expression, $new, $context);
  ');
  $handlers['postinc']= &opcode('
    $new= value($node->expression, $context);
    set($node->expression, $new+ 1, $context);
  ');
  $handlers['if']= &opcode('
    // if (condition) { if-statements } [ elseif { elseif-statements }] [ else { else-statements }]
    if (value($node->condition, $context)) {

      // condition: true
      $block= &$node->statements;
    } else if ($node->elseif) {

      // condition: false, else if
      if (value($node->elseif->args[0], $context)) {
        $block= &$node->elseif->args[1];
      } else {
        $block= &$node->else;
      }
    } else if ($node->else) {

      // condition: false, else
      $block= &$node->else;
    }

    foreach ($block as $arg) {
      handle($arg, $context);
    }
  ');
  $handlers['for']= &opcode('
    // for (init; condition; loop) { statements }
    // init
    foreach ($node->init as $arg) {
      handle($arg, $context);
    }
    
    while (value($node->condition[0], $context)) {  // condition
    
      // statements 
      foreach ($node->statements as $arg) {
        handle($arg, $context);
      }
      
      // loop
      handle($node->loop[0], $context);
    }
  ');
  $handlers['while']= &opcode('
    // while (condition) { statements }
    while (value($node->condition, $context)) {  // condition

      // statements 
      foreach ($node->statements as $arg) {
        handle($arg, $context);
      }
    }
  ');
  $handlers['dowhile']= &opcode('
    // do { statements } while (condition)
    do {

      // statements 
      foreach ($node->statements as $arg) {
        handle($arg, $context);
      }
    } while (value($node->condition, $context));
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
    if (isset($node->expression)) {
      $context["exitcode"]= value($node->expression, $context);
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
    $context["classes"][$node->name]= $node;
  ');
  $handlers['functiondeclaration']= &opcode('
    $context["functions"][$node->name]= $node;
  ');
  $handlers['functioncall']= &opcode('
    function_exists($node->name) 
      ? builtincall($node, $context)
      : functioncall($node, $context)
    ;
  ');
  $handlers['methodcall']= &opcode('
    methodcall($node, $context);
  ');
  $handlers['try']= &opcode('
    execute($node->statements, $context);
    $oE= &$context["E"];
    $context["E"] && handle($node->catch, $context);
    
    // If Exception was not caught by `catch`, pass it to the
    // next catch (if available).
    // Do not pass a newly thrown exception to these catches (thus comparing
    // the ids).
    if (NULL !== $node->catch->catches && $context["E"] && $oE->id === $context["E"]->id) {
      execute($node->catch->catches, $context);
    }
    
    // Execute finally
    if (NULL !== $node->finally) {
      execute($node->finally->statements, $context);
    }
  ');
  $handlers['catch']= &opcode('
    $pointer= &$context["E"];
    $class= $GLOBALS["objects"][$pointer->id]["name"];
    
    if ($node->class == $class) {   // FIXME: Use Instance
      $context["variables"][$node->variable]= &value($context["E"], $context);
      unset($context["E"]);
      execute($node->statements, $context);
    }
  ');
  $handlers['throw']= &opcode('
    except(value($node->value, $context), $context);
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
  $context['variables']['$argc']= $argc- 1;
  $context['variables']['$argv']= $argv;
  
  execute($nodes, $context);
  
  $context['E'] && error(E_ERROR, '*** Uncaught '.$context['E']->toString().' ('.$GLOBALS["objects"][$context["E"]->id]["name"].')');
  // }}}
?>
