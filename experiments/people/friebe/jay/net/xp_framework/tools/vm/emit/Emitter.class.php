<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$
 */
 
  uses('net.xp_framework.tools.vm.CompileError');
 
  /**
   * Emitter
   *
   * @purpose  Abstract base class
   */
  class Emitter extends Object {
    var
      $errors= array();

    /**
     * Emits an array of nodes
     *
     * @access  public
     * @param   net.xp_framework.tools.vm.VNode[] nodes
     */
    function emitAll($nodes) {
      foreach ($nodes as $node) $this->emit($node);
    }
    
    /**
     * Adds an error
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.CompileError error
     */
    function addError(&$error) {
      $this->errors[]= &$error;
    }
    
    /**
     * Returns whether errors have occured
     *
     * @access  public
     * @return  bool
     */
    function hasErrors() {
      return !empty($this->errors);
    }

    /**
     * Returns whether errors have occured
     *
     * @access  public
     * @return  net.xp_framework.tools.vm.CompileError[]
     */
    function getErrors() {
      return $this->errors;
    }
    
    /**
     * Retrieves result 
     *
     * @access  public
     * @return  string
     */
    function getResult() { }

    /**
     * Emits a single node
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emit(&$node) {
      if (is_a($node, 'VNode')) {
        $func= 'emit'.ucfirst(substr(get_class($node), 0, -4));
        if (!method_exists($this, $func)) {
          $this->cat && $this->cat->error('Cannot handle', $node);
          $this->addError(new CompileError(100, 'Cannot handle '.$node->getClassName()));
          return;
        }

        $this->cat && $this->cat->debug($func, '(', $node->getClassName(), ')');
        $this->$func($node);
        return;
      } else if (is_array($node)) {
        $this->emitArray($node);
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

    /**
     * Emits an array
     *
     * @access  public
     * @param   array array
     */
    function emitArray($array) { }
    
    /**
     * Emits a string
     *
     * @access  public
     * @param   string string
     */
    function emitString($string) { }

    /**
     * Emits an integer
     *
     * @access  public
     * @param   int integer
     */
    function emitInteger($integer) { }

    /**
     * Emits a double
     *
     * @access  public
     * @param   double double
     */
    function emitDouble($double) {  }
    
    /**
     * Emits a boolean
     *
     * @access  public
     * @param   bool bool
     */
    function emitBoolean($bool) { }

    /**
     * Emits a null
     *
     * @access  public
     */
    function emitNull() { }

    /**
     * Emits PackageDeclarations
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitPackageDeclaration(&$node) { }

    /**
     * Emits FunctionDeclarations
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitFunctionDeclaration(&$node) { }

    /**
     * Emits MethodDeclarations
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitMethodDeclaration(&$node) { }

    /**
     * Emits ConstructorDeclarations
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitConstructorDeclaration(&$node) { }

    /**
     * Emits ClassDeclarations
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitClassDeclaration(&$node) { }

    /**
     * Emits FunctionCalls
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitFunctionCall(&$node) { }

    /**
     * Emits MethodCalls
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitMethodCall(&$node) { }

    /**
     * Emits Nots
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitNot(&$node) { }

    /**
     * Emits ObjectReferences
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitObjectReference(&$node) { }

    /**
     * Emits Binarys
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitBinary(&$node) { }

    /**
     * Emits Variables
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitVariable(&$node) { }

    /**
     * Emits Assigns
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitAssign(&$node) { }

    /**
     * Emits BinaryAssigns
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitBinaryAssign(&$node) { }

    /**
     * Emits Ifs
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitIf(&$node) { }

    /**
     * Emits Catchs
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitCatch(&$node) { }

    /**
     * Emits Exits
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitExit(&$node) { }

    /**
     * Emits News
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitNew(&$node) { }

    /**
     * Emits Trys
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitTry(&$node) { }

    /**
     * Emits Echos
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitEcho(&$node) { }

    /**
     * Emits Returns
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitReturn(&$node) { }

    /**
     * Emits Ternarys
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitTernary(&$node) { }
    
    /**
     * Emits Fors
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitFor(&$node) { }

    /**
     * Emits PostIncs
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitPostInc(&$node) { }
    
    /**
     * Emits MemberDeclarationLists
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitMemberDeclarationList(&$node) { }
    
    /**
     * Emits OperatorDeclarations
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitOperatorDeclaration(&$node) { }

    /**
     * Emits Throws
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitThrow(&$node) { }   
    
    /**
     * Emits Imports
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitImport(&$node) { }

    /**
     * Emits InstanceOfs
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitInstanceOf(&$node) { }

    /**
     * Emits ClassReferences
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitClassReference(&$node) { }

    /**
     * Emits InterfaceDeclarations
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitInterfaceDeclaration(&$node) { }

    /**
     * Emits Whiles
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitWhile(&$node) { }
 
    /**
     * Emits Do ... Whiles
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitDoWhile(&$node) { }

    /**
     * Emits Foreachs
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitForeach(&$node) { }

    /**
     * Emits Switches
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitSwitch(&$node) { }

    /**
     * Emits Cases
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitCase(&$node) { }

    /**
     * Emits Defaults
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitDefault(&$node) { }

    /**
     * Emits Breaks
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitBreak(&$node) { }

    /**
     * Emits Continues
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emitContinue(&$node) { }
    
    /**
     * Set a logger category for debugging
     *
     * @access  public
     * @param   util.log.LogCategory cat
     */
    function setTrace($cat) {
      $this->cat= $cat;
    }

  } implements(__FILE__, 'util.log.Traceable');
?>
