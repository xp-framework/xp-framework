<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.checks.Check', 'xp.compiler.ast.NativeImportNode');

  /**
   * Emits a warning when native import are used
   *
   * @test    xp://net.xp_lang.tests.checks.NoNativeImportsTest
   */
  class NoNativeImports extends Object implements Check {

    /**
     * Return node this check works on
     *
     * @return  lang.XPClass<? extends xp.compiler.ast.Node>
     */
    public function node() {
      return XPClass::forName('xp.compiler.ast.NativeImportNode');
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
      return array('N415', 'Native imports ('.cast($node, 'xp.compiler.ast.NativeImportNode')->name.') make code non-portable');
    }
  }
?>
