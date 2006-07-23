<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$
 */
 
  class Emitter extends Object {

    function emitAll($nodes) {
      foreach ($nodes as $node) $this->emit($node);
    }
    
    function getResult() { }

    function emit(&$node) {
      if (is_a($node, 'VNode')) {
        $func= 'emit'.ucfirst(substr(get_class($node), 0, -4));
        if (!method_exists($this, $func)) {
          Console::writeLine('*** Cannot handle ', $node->getClassName());
          return;
        }

        Console::writeLine('+++ ', $func, '(', $node->getClassName(), ')');
        $this->$func($node, $opcodes, $blocks, $symbols, $types);
        return;
      } else if ('"' == $node{0}) { // Double-quoted string
        $value= '';
        for ($i= 1, $s= strlen($node)- 1; $i < $s; $i++) {
          if ('\\' == $node{$i}) {
            switch ($node{$i+ 1}) {
              case 'r': $value.= "\r"; break;
              case 'n': $value.= "\n"; break;
              case 't': $value.= "\t"; break;
            }
            $i++;
          } else {
            $value.= $node{$i};
          }
        }
        $this->emitString($value);
      } else if ("'" == $node{0}) { // Single-quoted string
        $this->emitString(substr($node, 1, -1));
      } else if (is_int($node)) {
        $this->emitInteger($node);
      } else if (is_float($node)) {
        $this->emitDouble($node);
      } else switch (strtolower($node)) {
        case 'true': $this->emitBoolean(TRUE); break;
        case 'false': $this->emitBoolean(FALSE); break;
        case 'null': $this->emitNull(); break;
      }
    }
    
    function emitString($string) { }

    function emitInteger($integer) { }

    function emitDouble($double) { }
 
    function emitBoolean($bool) { }

    function emitNull() { }

    function emitPackageDeclaration(&$node) { }

    function emitFunctionDeclaration(&$node) { }

    function emitMethodDeclaration(&$node) { }

    function emitConstructorDeclaration(&$node) { }

    function emitClassDeclaration(&$node) { }

    function emitFunctionCall(&$node) { }

    function emitMethodCall(&$node) { }

    function emitNot(&$node) { }

    function emitObjectReference(&$node) { }

    function emitBinary(&$node) { }

    function emitVariable(&$node) { }

    function emitAssign(&$node) { }

    function emitBinaryAssign(&$node) { }

    function emitIf(&$node) { }

    function emitCatch(&$node) { }

    function emitExit(&$node) { }

    function emitNew(&$node) { }

    function emitTry(&$node) { }

    function emitEcho(&$node) { }

    function emitReturn(&$node) { }

    function emitTernary(&$node) { }
    
    function emitFor(&$node) { }

    function emitPostInc(&$node) { }
    
    function emitMemberDeclarationList(&$node) { }
  }
?>
