<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.compiler.checks.Check', 'xp.compiler.ast.RoutineNode');

  /**
   * Verifies routines
   *
   * @test    xp://net.xp_lang.tests.checks.FieldsVerificationTest
   */
  class FieldsVerification extends Object implements Check {

    /**
     * Return node this check works on
     *
     * @return  lang.XPClass<? extends xp.compiler.ast.Node>
     */
    public function node() {
      return XPClass::forName('xp.compiler.ast.FieldNode');
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
      $field= cast($node, 'xp.compiler.ast.FieldNode');

      if ($scope->declarations[0] instanceof InterfaceNode) {
        return array('I403', 'Interfaces may not have field declarations');
      }
    }
  }
?>
