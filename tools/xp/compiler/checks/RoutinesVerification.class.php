<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.compiler.checks.Check', 'xp.compiler.ast.RoutineNode');

  /**
   * Verifies routines
   *
   * @test    xp://tests.checks.RoutinesVerificationTest
   */
  class RoutinesVerification extends Object implements Check {

    /**
     * Return node this check works on
     *
     * @return  lang.XPClass<? extends xp.compiler.ast.Node>
     */
    public function node() {
      return XPClass::forName('xp.compiler.ast.RoutineNode');
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
      $routine= cast($node, 'xp.compiler.ast.RoutineNode');

      $qname= $scope->declarations[0]->name->compoundName().'::'.$routine->getName();
      $empty= $routine->body === NULL;
      if ($scope->declarations[0] instanceof InterfaceNode) {
        if (!$empty) {
          return array('R403', 'Interface methods may not have a body '.$qname);
        } else if (MODIFIER_PUBLIC != $routine->modifiers) {
          return array('R401', 'Interface methods may only be public '.$qname);
        }
      } else {
        if (Modifiers::isAbstract($routine->modifiers) && !$empty) {
          return array('R403', 'Abstract methods may not have a body '.$qname);
        } else if (!Modifiers::isAbstract($routine->modifiers) && $empty) {
          return array('R401', 'Non-abstract methods must have a body '.$qname);
        }
      }
    }
  }
?>
