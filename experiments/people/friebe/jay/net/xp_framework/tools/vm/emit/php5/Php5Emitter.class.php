<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$
 */
 
  uses('net.xp_framework.tools.vm.emit.Emitter');
 
  class Php5Emitter extends Emitter {
    var
      $bytes   = '',
      $context = array();
      
    function __construct() {
      $this->bytes= "<?php\n  require('php5-emit/__xp__.php');\n  ";
      $this->context['package']= '';
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
      return strtr((strstr($class, '~') ? '' : $this->context['package']).$class , '~', '·');
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

    function emitConstructorDeclaration(&$node) { 
      $this->bytes.= 'function __construct(';
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
      
      $this->bytes.= 'class '.$this->qualifiedName($node->name).' extends ';
      $this->bytes.= $this->qualifiedName($node->extends ? $node->extends : 'xp~lang~Object');
      $this->bytes.= '{';
      foreach ($node->statements as $node) {
        $this->emit($node);
      }
      
      // Property simulation via __get / __set
      if ($this->context['properties']) {
        $this->bytes.= "\n".'static $__properties= array(';
        foreach ($this->context['properties'] as $property) {
          $this->bytes.= "\n  '".substr($property->args[0], 1)."' => array(".
            ($property->args[2]['get'] ? "'".$property->args[2]['get']."'" : 'NULL').', '.
            ($property->args[2]['set'] ? "'".$property->args[2]['set']."'" : 'NULL').
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
      } else if ($node->class) {          // Instance
        $this->emit($node->class);    
        $this->bytes.= '->';
      } else {                            // Chains
        $this->bytes.= '->';
      }
 
      $this->bytes.= $node->method.'(';
      foreach ($node->arguments as $arg) {
        $this->emit($arg);
        $this->bytes.= ', ';
      }
      $node->arguments && $this->bytes= substr($this->bytes, 0, -2);
      $this->bytes.= ')';
 
      foreach ($node->chain as $node) {
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

    function emitCatch(&$node) { 
      $this->bytes.= '} catch (XPException $__e) { ';
      $this->bytes.= 'if ($__e->cause instanceof '.$this->qualifiedName($node->class).') { ';
      $this->bytes.= $node->variable.'= $__e->cause; ';
      $this->emitAll($node->statements);
      $this->bytes.= '} else { throw $__e; } }';
      // TODO more catches
    }

    function emitExit(&$node) { 
      $this->bytes.= 'throw xp::exception(new xp·lang·SystemExit(';
      $node->expression && $this->emit($node->expression);
      $this->bytes.= '))';
    }

    function emitNew(&$node) {
      $node->instanciation->chain && $this->bytes.= 'xp::create(';
      $this->bytes.= 'new '.$this->qualifiedName($node->class->name).'(';
      foreach ($node->instanciation->arguments as $arg) {
        $this->emit($arg);
        $this->bytes.= ', ';
      }
      $node->instanciation->arguments && $this->bytes= substr($this->bytes, 0, -2);
      $this->bytes.= ')';
      if (!$node->instanciation->chain) return;
      
      $this->bytes.= ')';
      foreach ($node->instanciation->chain as $node) {
        $this->emit($node);
      }
    }

    function emitTry(&$node) { 
      $this->bytes.= "try {\n  ";
      $this->emitAll($node->statements);
      $this->emit($node->catch);
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
        if ($member->args[2]) {
          $this->context['properties'][]= $member;
        } else {
          $members.= $member->args[0].', ';   // FIXME: Still a PNode
          $m++;
        }
      }
      $members && $this->bytes.= implode(' ', $this->modifierNames($node->modifiers)).' '.substr($members, 0, -2).';';
    }
  }
?>
