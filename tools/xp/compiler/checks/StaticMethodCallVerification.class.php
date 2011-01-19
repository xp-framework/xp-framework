<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.checks.AbstractMethodCallVerification', 'xp.compiler.ast.StaticMethodCallNode');

  /**
   * Verifies static method calls
   *
   * @test    xp://net.xp_lang.tests.checks.StaticMethodCallVerificationTest
   */
  class StaticMethodCallVerification extends AbstractMethodCallVerification {

    /**
     * Return node this check works on
     *
     * @return  lang.XPClass<? extends xp.compiler.ast.Node>
     */
    public function node() {
      return XPClass::forName('xp.compiler.ast.StaticMethodCallNode');
    }

    /**
     * Return whether this check is to be run deferred
     *
     * @return  bool
     */
    public function defer() {
      return FALSE;
    }
    
    /**
     * Executes this check
     *
     * @param   xp.compiler.ast.Node node
     * @param   xp.compiler.types.Scope scope
     * @return  bool
     */
    public function verify(xp·compiler·ast·Node $node, Scope $scope) {
      $call= cast($node, 'xp.compiler.ast.StaticMethodCallNode');
      return $this->verifyMethod($call->type, $call->name, $scope);
    }
  }
?>
