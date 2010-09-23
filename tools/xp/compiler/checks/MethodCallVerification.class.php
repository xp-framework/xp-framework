<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.checks.Check', 'xp.compiler.ast.MethodCallNode', 'xp.compiler.types.TypeInstance');

  /**
   * Verifies method calls
   *
   * @test    xp://net.xp_lang.tests.checks.MethodCallVerificationTest
   */
  class MethodCallVerification extends Object implements Check {

    /**
     * Return node this check works on
     *
     * @return  lang.XPClass<? extends xp.compiler.ast.Node>
     */
    public function node() {
      return XPClass::forName('xp.compiler.ast.MethodCallNode');
    }

    /**
     * Return whether this check is to be run deferred
     *
     * @return  bool
     */
    public function defer() {
      return TRUE;
    }
    
    /**
     * Executes this check
     *
     * @param   xp.compiler.ast.Node node
     * @param   xp.compiler.types.Scope scope
     * @return  bool
     */
    public function verify(xp·compiler·ast·Node $node, Scope $scope) {
      $call= cast($node, 'xp.compiler.ast.MethodCallNode');

      // Verify type
      // * var: Might have method
      // * primitive, array, map, int: Definitely don't have methods
      $type= $scope->typeOf($call->target);
      if ($type->isVariable()) {
        return array('T203', 'Member call (var).'.$call->name.'() verification deferred until runtime');
      } else if (!$type->isClass()) {
        return array('T305', 'Using member calls on unsupported type '.$type->compoundName());
      }

      // Verify target method exists
      $target= new TypeInstance($scope->resolveType($type));
      if (!$target->hasMethod($call->name)) {
        return array('T404', 'No such method '.$call->name.'() in '.$target->name());
      }
      
      // Verify visibility
      $method= $target->getMethod($call->name);
      if (!($method->modifiers & MODIFIER_PUBLIC)) {
        $enclosing= $scope->resolveType($scope->declarations[0]->name);
        if (
          ($method->modifiers & MODIFIER_PRIVATE && !$enclosing->equals($target)) ||
          ($method->modifiers & MODIFIER_PROTECTED && !($enclosing->equals($target) || $enclosing->isSubclassOf($target)))
        ) {
          return array('T403', sprintf(
            'Invoking %s %s::%s() from %s',
            implode(' ', Modifiers::namesOf($method->modifiers)),
            $target->name(),
            $method->name,
            $enclosing->name()
          ));
        }
      }
    }
  }
?>
