<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.checks.AbstractMethodCallVerification', 'xp.compiler.ast.MethodCallNode');

  /**
   * Verifies method calls
   *
   * @test    xp://net.xp_lang.tests.checks.MethodCallVerificationTest
   */
  class MethodCallVerification extends AbstractMethodCallVerification {

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

      return $this->verifyMethod($type, $call->name, $scope);
    }
  }
?>
