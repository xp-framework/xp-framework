<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'xp.compiler.ast.MethodCallNode',
    'xp.compiler.optimize.InliningOptimization'
  );

  /**
   * Inlines method calls
   *
   * @see   xp://xp.compiler.optimize.InliningOptimization
   */
  class InlineMethodCallsOptimization extends InliningOptimization {
    
    /**
     * Return node this optimization works on
     *
     * @return  lang.XPClass<? extends xp.compiler.ast.Node>
     */
    public function node() {
      return XPClass::forName('xp.compiler.ast.MethodCallNode');
    }
    
    /**
     * Optimize a given node
     *
     * @param   xp.compiler.ast.Node in
     * @param   xp.compiler.types.Scope scope
     * @param   xp.compiler.optimize.Optimizations optimizations
     * @param   xp.compiler.ast.Node optimized
     */
    public function optimize(xp·compiler·ast·Node $in, Scope $scope, Optimizations $optimizations) {
      $call= cast($in, 'xp.compiler.ast.MethodCallNode');

      // Online inline calls inside this class
      return $this->isLocal($scope, $scope->typeOf($call->target))
        ? $this->inline($call, $scope, $optimizations)
        : $call
      ;
    }
  }
?>
