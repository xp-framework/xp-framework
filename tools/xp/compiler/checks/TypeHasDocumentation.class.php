<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.compiler.checks.Check', 'xp.compiler.ast.TypeDeclarationNode');

  /**
   * Check whether api documentation is available for a type, that
   * is: interfaces, classes and enums.
   *
   * @test    xp://tests.checks.TypeHasDocumentationTest
   */
  class TypeHasDocumentation extends Object implements Check {

    /**
     * Return node this check works on
     *
     * @return  lang.XPClass<? extends xp.compiler.ast.Node>
     */
    public function node() {
      return XPClass::forName('xp.compiler.ast.TypeDeclarationNode');
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
      $decl= cast($node, 'xp.compiler.ast.TypeDeclarationNode');
      if (!isset($decl->comment) && !$decl->synthetic) {
        return array('D201', 'No api doc for type '.$decl->name->compoundName());
      }
    }
  }
?>
