<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.compiler.checks.Check', 'xp.compiler.ast.MethodNode', 'xp.compiler.ast.ReturnNode');

  /**
   * Check whether a method with inline modifier actually is inlineable
   *
   * @see   xp://xp.compiler.optimize.InliningOptimization
   * @test  xp://net.xp_lang.tests.checks.IsInlineableTest
   */
  class IsInlineable extends Object implements Check {

    /**
     * Return node this check works on
     *
     * @return  lang.XPClass<? extends xp.compiler.ast.Node>
     */
    public function node() {
      return XPClass::forName('xp.compiler.ast.MethodNode');
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
      $m= cast($node, 'xp.compiler.ast.MethodNode');
      if (!($m->modifiers & MODIFIER_INLINE)) return;  // Ignore these
      
      // Body must consist of a one line...
      if (1 !== ($s= sizeof($m->body))) {
        return array('I402', 'Only one-liners can be inlined: '.$m->name.'() has '.$s.' statements');
      }
      
      // ...which must be of the form "return <EXPR>;"
      if (!($m->body[0] instanceof ReturnNode)) {
        return array('I403', 'Only one-line return statements can be inlined: '.$m->name.'()');
      }
      
      // OK
      return NULL;
    }
  }
?>
