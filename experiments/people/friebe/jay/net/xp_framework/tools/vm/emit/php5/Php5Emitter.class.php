<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$
 */
 
  uses('net.xp_framework.tools.vm.emit.Emitter');
 
  class Php5Emitter extends Emitter {
    var
      $bytes   = '',
      $context = array(),
      $operators= array(
        '*' => 'times',
        '+' => 'plus',
        '-' => 'minus',
        '/' => 'divide'
      );
      
    function __construct() {
      $this->bytes= "<?php\n  require('php5-emit/__xp__.php');\n  ";
      $this->context['package']= '';
      $this->contect['overloaded']= array();
    }

    function modifierNames($m) {
      $names= array();
      if ($m & MODIFIER_ABSTRACT) $names[]= 'abstract';
      if ($m & MODIFIER_FINAL) $names[]= 'final';
      switch ($m & (MODIFIER_PUBLIC | MODIFIER_PROTECTED | MODIFIER_PRIVATE)) {
        case MODIFIER_PRIVATE: $names[]= 'private'; break;
        case MODIFIER_PROTECTED: $names[]= 'protected'; break;
        case MODIFIER_PUBLIC:
        default: $names[]= 'public'; break;
      }
      if ($m & MODIFIER_STATIC) $names[]= 'static';
      return $names;
    }
    
    function qualifiedName($class) {
      return strtr((strstr($class, '~') ? $class : $this->prefixedClassnameFor($class)), '~', '·');
    }
    
    function prefixedClassnameFor($class) {
      if (isset($this->context['imports'][$this->context['package']][$class]))
        return $this->context['imports'][$this->context['package']][$class];
        
      return $this->context['package'].$class;
    }
    
    function typeOf(&$node) {
      if (is_a($node, 'NewNode')) {
        return $node->class->name;
      } else if (is_a($node, 'VariableNode')) {
        return $this->context['types'][$node->name];
      } else if (is_a($node, 'MethodCallNode')) {
        return $this->context['types'][$node->class.'::'.$node->method];
      } else if (is_a($node, 'ParameterNode')) {
        return $node->type;
      } else if (is_a($node, 'BinaryNode')) {
        // TODO: Check operator overloading
        return NULL;
      } else if (is_a($node, 'ObjectReferenceNode')) {
        // TODO: Check class member type
        return NULL;
      } else if ('"' == $node{0}) { // Double-quoted string
        return 'string';
      } else if ("'" == $node{0}) { // Single-quoted string
        return 'string';
      } else if (is_int($node)) {
        return 'integer';
      } else if (is_float($node)) {
        return 'double';
      } else switch (strtolower($node)) {
        case 'true': return 'bool';
        case 'false': return 'bool';
        case 'null': return 'object';
      }
      
      Console::writeLine('*** Cannot defer type from ', xp::stringOf($node));
      return NULL;  // Unknown
    }
    
    function hasAnnotation(&$node, $name) {
      foreach ($node->annotations as $annotation) {       // FIXME: PNode[]
        if ($annotation->args[0] == $name) return TRUE;
      }
      return FALSE;
    }
    
    function methodName(&$node) {
      if (!$this->hasAnnotation($node, 'overloaded')) return $node->name;

      $this->contect['overloaded'][$this->context['class'].'::'.$node->name]= TRUE;
      $name= $node->name;
      foreach ($node->parameters as $param) {
        $name.= $this->typeOf($param);
      }
      return $name;
    }

    function emitAll($nodes) {
      foreach ($nodes as $node) {
        $this->emit($node);
        $this->bytes.= ";\n  ";
      }
    }
    
    function getResult() { 
      return $this->bytes."\n?>";
    }
    
    function emitString($string) { 
      $this->bytes.= "'".str_replace('\'', '\\\'', $string)."'";
    }

    function emitInteger($integer) { 
      $this->bytes.= $integer;
    }

    function emitDouble($double) {
      $this->bytes.= $double;
    }
    
    function emitBoolean($bool) { 
      $this->bytes.= $bool ? 'TRUE' : 'FALSE';
    }

    function emitNull() { 
      $this->bytes.= 'xp::$null';
    }

    function emitPackageDeclaration(&$node) { 
      $this->context['package']= $node->name.'~';
      foreach ($node->statements as $node) {
        $this->emit($node);
      }
      $this->context['package']= NULL;
    }

    function emitFunctionDeclaration(&$node) { 
      $this->bytes.= 'function '.$node->name.'(';
      foreach ($node->parameters as $param) {
        $this->bytes.= $param->name;
        if ($param->default) {
          $this->bytes.= '= ';
          $this->emit($param->default);
        }
        $this->bytes.= ', ';
      }
      $node->parameters && $this->bytes= substr($this->bytes, 0, -2);
      $this->bytes.= ') {';
      $this->emitAll($node->statements);
      $this->bytes.= '}';
    }

    function emitMethodDeclaration(&$node) {
      $this->context['types'][$this->context['class'].'::'.$node->name]= $node->return;

      $this->bytes.= 'function '.$this->methodName($node).'(';
      foreach ($node->parameters as $param) {
        $this->bytes.= $param->name;
        if ($param->default) {
          $this->bytes.= '= ';
          $this->emit($param->default);
        }
        $this->bytes.= ', ';
      }
      $node->parameters && $this->bytes= substr($this->bytes, 0, -2);
      $this->bytes.= ') {';
      $this->emitAll($node->statements);
      $this->bytes.= '}';
    }

    function emitConstructorDeclaration(&$node) { 
      $this->bytes.= 'function '.$this->methodName($node).'(';
      foreach ($node->parameters as $param) {
        $this->bytes.= $param->name;
        if ($param->default) {
          $this->bytes.= '= ';
          $this->emit($param->default);
        }
        $this->bytes.= ', ';
      }
      $node->parameters && $this->bytes= substr($this->bytes, 0, -2);
      $this->bytes.= ') {';
      $this->emitAll($node->statements);
      $this->bytes.= '}';
    }

    function emitClassDeclaration(&$node) {
      $this->context['properties']= array();
      $this->context['class']= $this->qualifiedName($node->name);
      $this->context['operators'][$this->context['class']]= array();
      
      $this->bytes.= 'class '.$this->context['class'].' extends ';
      $this->bytes.= $this->qualifiedName($node->extends ? $node->extends : 'xp~lang~Object');
      $this->bytes.= '{';
      foreach ($node->statements as $node) {
        $this->emit($node);
      }
      
      // Property simulation via __get / __set
      if ($this->context['properties']) {
        $this->bytes.= "\n".'static $__properties= array(';
        foreach ($this->context['properties'] as $property) {
          $this->bytes.= "\n  '".substr($property->name, 1)."' => array(".
            ($property->accessors['get'] ? "'".$property->accessors['get']."'" : 'NULL').', '.
            ($property->accessors['set'] ? "'".$property->accessors['set']."'" : 'NULL').
          '),';
        }
        $this->bytes.= ');';
        $this->bytes.= '
          function __get($name) {
            if (!isset(self::$__properties[$name])) die(\'Read of non-existant property "\'.$name.\'"\');
            if (NULL === self::$__properties[$name][0]) {
              throw xp::exception(new xp·lang·IllegalAccessException(\'Cannot access property "\'.$name.\'"\'));
            } else if (\'$\' == self::$__properties[$name][0][0]) {
              return $this->{substr(self::$__properties[$name][0], 1)};
            } else {
              return $this->{self::$__properties[$name][0]}();
            }
          }

          function __set($name, $value) {
            if (!isset(self::$__properties[$name])) die(\'Write of non-existant property "\'.$name.\'"\');
            if (NULL === self::$__properties[$name][1]) {
              throw xp::exception(new xp·lang·IllegalAccessException(\'Cannot access property "\'.$name.\'"\'));
            } else if (\'$\' == self::$__properties[$name][1][0]) {
              $this->{substr(self::$__properties[$name][1], 1)}= $value;
            } else {
              $this->{self::$__properties[$name][1]}($value);
            }
          }
        ';
      }

      $this->bytes.= '}';
    }

    function emitFunctionCall(&$node) { 
      $this->bytes.= $node->name.'(';
      foreach ($node->arguments as $arg) {
        $this->emit($arg);
        $this->bytes.= ', ';
      }
      $node->arguments && $this->bytes= substr($this->bytes, 0, -2);
      $this->bytes.= ')';
    }

    function emitMethodCall(&$node) {
      if (is_string($node->class)) {      // Static
        $this->bytes.= $node->class.'::';
        $type= $node->class;
      } else if ($node->class) {          // Instance
        $this->emit($node->class);    
        $type= $this->typeOf($node->class);
        $this->bytes.= '->';
      } else {                            // Chains
        $type= $this->typeOf($this->context['chain_prev']);
        $this->bytes.= '->';
      }
 
      $method= $node->method;
      if ($this->contect['overloaded'][$type.'::'.$node->method]) {
        foreach ($node->arguments as $arg) {
          $method.= $this->typeOf($arg);
        }
      }
      $this->bytes.= $method.'(';
      foreach ($node->arguments as $arg) {
        $this->emit($arg);
        $this->bytes.= ', ';
      }
      $node->arguments && $this->bytes= substr($this->bytes, 0, -2);
      $this->bytes.= ')';
 
      foreach ($node->chain as $node) {
        $this->context['chain_prev']= $node;
        $this->emit($node);
      }
    }

    function emitNot(&$node) { 
      $this->bytes.= '!';
      $this->emit($node->expression);
    }

    function emitObjectReference(&$node) { 
      $this->emit($node->class);
      $this->bytes.= '->'.$node->member;
    }

    function emitBinary(&$node) {
      $type= $this->typeOf($node->left);
      
      // Check for operator overloading
      if (isset($this->context['operators'][$type][$node->operator])) {
        return $this->emitMethodCall(new MethodCallNode(
          $type, 
          '__operator'.$this->operators[$node->operator],
          array($node->left, $node->right)
        ));
      }
      
      // Regular operator
      $this->emit($node->left);
      $this->bytes.= $node->operator;
      $this->emit($node->right);
    }

    function emitVariable(&$node) { 
      $this->bytes.= $node->name;
      if (NULL === $node->offset) return;

      $this->bytes.= '[';
      $this->emit($node->offset);
      $this->bytes.= ']';
    }

    function emitAssign(&$node) { 
      $this->emit($node->variable);
      $this->bytes.= '= ';
      $this->emit($node->expression);

      $this->context['types'][$node->variable->name]= $this->typeOf($node->expression);
    }

    function emitBinaryAssign(&$node) { 
      $this->emit($node->variable);
      $this->bytes.= $node->operator.'=';
      $this->emit($node->expression);
    }

    function emitIf(&$node) { 
      $this->bytes.= 'if (';
      $this->emit($node->condition);
      $this->bytes.= ') {';
      $this->emitAll($node->statements);
      $this->bytes.= '}';
    }

    function emitExit(&$node) { 
      $this->bytes.= 'throw xp::exception(new xp·lang·SystemExit(';
      $node->expression && $this->emit($node->expression);
      $this->bytes.= '))';
    }

    function emitNew(&$node) {
      if ($node->instanciation->declaration) {
        $this->bytes.= 'xp::instance(\''.$this->qualifiedName($node->class->name).'\', array(';
        foreach ($node->instanciation->arguments as $arg) {
          $this->emit($arg);
          $this->bytes.= ', ';
        }
        $node->instanciation->arguments && $this->bytes= substr($this->bytes, 0, -2);
        $this->bytes.= '), \'{';
        $b= $this->bytes;

        $this->bytes= '';
        foreach ($node->instanciation->declaration as $decl) {
          $this->emit($decl);
        }

        $this->bytes= $b.str_replace('\'', '\\\'', $this->bytes).'}\'';
        $node->instanciation->chain || $this->bytes.= ')';
      } else {
        $node->instanciation->chain && $this->bytes.= 'xp::create(';
        
        if ($this->contect['overloaded'][$node->class->name.'::__construct']) {
          $ctor= '__construct';
          foreach ($node->instanciation->arguments as $arg) {
            $ctor.= $this->typeOf($arg);
          }
          $this->bytes.= 'xp::spawn(\''.$this->qualifiedName($node->class->name).'\', \''.$ctor.'\', array(';
          foreach ($node->instanciation->arguments as $arg) {
            $this->emit($arg);
            $this->bytes.= ', ';
          }
          $node->instanciation->arguments && $this->bytes= substr($this->bytes, 0, -2);
          $this->bytes.= '))';
        } else {
          $this->bytes.= 'new '.$this->qualifiedName($node->class->name).'(';
          foreach ($node->instanciation->arguments as $arg) {
            $this->emit($arg);
            $this->bytes.= ', ';
          }
          $node->instanciation->arguments && $this->bytes= substr($this->bytes, 0, -2);
          $this->bytes.= ')';
        }
      }

      if (!$node->instanciation->chain) return;
      $this->bytes.= ')';
      foreach ($node->instanciation->chain as $node) {
        $this->emit($node);
      }
    }
    
    function emitImportList(&$node) {
      $this->emit($node->list);
    }
    
    function emitImport(&$node) {
    
      // Calculate destination name if none was supplied
      if (NULL === $node->destination) {
        $node->destination= substr($node->source, strrpos($node->source, '~')+ 1);
      }
      
      // Register import
      $this->context['imports'][$this->context['package']][$node->destination]= $node->source;
    }

    function emitTry(&$node) { 
      $this->bytes.= "try {\n  ";
      $this->emitAll($node->statements);      

      $this->bytes.= '} catch (XPException $__e) { ';
      $this->bytes.= 'if ($__e->cause instanceof '.$this->qualifiedName($node->catch->class).') { ';
      $this->bytes.= $node->catch->variable.'= $__e->cause; ';
      
      foreach ($node->catch->statements as $stmt) {
        if (is_a($stmt, 'ReturnNode') || is_a($stmt, 'ThrowNode')) {
          $node->finally && $this->emitAll($node->finally->statements);
        }
      
        $this->emit($stmt);
        $this->bytes.= ';';
      }
      
      foreach ($node->catch->catches as $catch) {
        $this->bytes.= '} else if ($__e->cause instanceof '.$this->qualifiedName($catch->class).') { ';
        $this->bytes.= $catch->variable.'= $__e->cause; ';
        $this->emitAll($catch->statements);
      }
      
      $this->bytes.= '} else { throw $__e; } }';
      
      $node->finally && $this->emitAll($node->finally->statements);
    }

    function emitEcho(&$node) {
      $this->bytes.= 'echo ';
      foreach ($node->args as $arg) {
        $this->emit($arg);
        $this->bytes.= ', ';
      }
      $this->bytes= substr($this->bytes, 0, -2);
    }

    function emitReturn(&$node) { 
      $this->bytes.= 'return ';
      $this->emit($node->value);
    }

    function emitTernary(&$node) {
      $this->emit($node->condition);
      $this->bytes.= ' ? ';
      $this->emit($node->expression);
      $this->bytes.= ' : ';
      $this->emit($node->conditional);
    }

    function emitFor(&$node) {
      $this->bytes.= 'for (';
      foreach ($node->init as $expr) {
        $this->emit($expr);
        $this->bytes.= ', ';
      }
      $node->init && $this->bytes= substr($this->bytes, 0, -2);
      $this->bytes.= ';';
 
      foreach ($node->condition as $expr) {
        $this->emit($expr);
        $this->bytes.= ', ';
      }
      $node->condition && $this->bytes= substr($this->bytes, 0, -2);
      $this->bytes.= ';';
 
      foreach ($node->loop as $expr) {
        $this->emit($expr);
        $this->bytes.= ', ';
      }
      $node->loop && $this->bytes= substr($this->bytes, 0, -2);

      $this->bytes.= ') {';
      $this->emitAll($node->statements);
      $this->bytes.= '}';
    }
    
    function emitPostInc(&$node) {
      $this->emit($node->expression);
      $this->bytes.= '++';
    }

    function emitMemberDeclarationList(&$node) { 
      $members= '';
      foreach ($node->members as $member) {
        if (is_a($member, 'PropertyDeclarationNode')) {
          $this->context['properties'][]= $member;
        } else {
          $members.= $member->name.', ';
          $m++;
        }
      }
      $members && $this->bytes.= implode(' ', $this->modifierNames($node->modifiers)).' '.substr($members, 0, -2).';';
    }

    function emitOperatorDeclaration(&$node) {       
      $this->context['operators'][$this->context['class']][$node->name]= TRUE;
      $this->bytes.= 'function __operator'.$this->operators[$node->name].'(';
      foreach ($node->parameters as $param) {
        $this->bytes.= $param->name;
        if ($param->default) {
          $this->bytes.= '= ';
          $this->emit($param->default);
        }
        $this->bytes.= ', ';
      }
      $node->parameters && $this->bytes= substr($this->bytes, 0, -2);
      $this->bytes.= ') {';
      $this->emitAll($node->statements);
      $this->bytes.= '}';
    }

    function emitThrow(&$node) {       
      $this->bytes.= 'throw xp::exception(';
      $this->emit($node->value);
      $this->bytes.= ')';
    }
  }
?>
