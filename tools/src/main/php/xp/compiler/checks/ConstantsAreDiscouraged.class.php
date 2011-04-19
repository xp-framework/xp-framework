<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.compiler.checks.Check', 'xp.compiler.ast.ConstantNode');

  /**
   * Emits a warning when global constants are used
   *
   * @test    xp://tests.checks.ConstantsAreDiscouragedTest
   */
  class ConstantsAreDiscouraged extends Object implements Check {

    /**
     * Return node this check works on
     *
     * @return  lang.XPClass<? extends xp.compiler.ast.Node>
     */
    public function node() {
      return XPClass::forName('xp.compiler.ast.ConstantNode');
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
      return array('T203', 'Global constants ('.cast($node, 'xp.compiler.ast.ConstantNode')->value.') are discouraged');
    }
  }
?>
