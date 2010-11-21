<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'xp.compiler.ast.AssignmentNode',
    'xp.compiler.ast.BinaryOpNode',
    'xp.compiler.ast.UnaryOpNode',
    'xp.compiler.optimize.Optimization'
  );

  /**
   * Optimizes assignments to binary operations where the left-hand side
   * is the variable to be assigned to to assignment shorthands, e.g.
   * 
   * <code>
   *   $a= $a + 1;    // Original
   *   $a+= 1;        // Optimized
   * </code>
   *
   * @test     xp://net.xp_lang.tests.optimization.AssignOptimizationTest
   */
  class AssignOptimization extends Object implements Optimization {
    protected static $optimizable= array(
      '~'   => '~=',
      '-'   => '-=',
      '+'   => '+=',
      '*'   => '*=',
      '/'   => '/=',
      '%'   => '%=',
      '<<'  => '<<=',
      '>>'  => '>>=',
      '&'   => '&=',
      '|'   => '|=',
      '^'   => '^='
    );      
    protected static $switch= array(
      '-='  => '+=',
      '+='  => '-='
    );

    /**
     * Return node this optimization works on
     *
     * @return  lang.XPClass<? extends xp.compiler.ast.Node>
     */
    public function node() {
      return XPClass::forName('xp.compiler.ast.AssignmentNode');
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
      $assign= cast($in, 'xp.compiler.ast.AssignmentNode');
      $assign->expression= $optimizations->optimize($assign->expression, $scope);

      // Optimize "<var>= <var>+ <expr>" to "<var>+= <expr>"
      if (
        $assign->expression instanceof BinaryOpNode && 
        isset(self::$optimizable[$assign->expression->op]) &&
        $assign->variable->equals($assign->expression->lhs)
      ) {
        $assign= new AssignmentNode(array(
          'variable'   => $assign->variable,
          'expression' => $assign->expression->rhs,
          'op'         => self::$optimizable[$assign->expression->op]
        ));
      }
      
      // Optimize "<var>-= -<expr>" to "<var>+= <expr>"
      // Optimize "<var>+= -<expr>" to "<var>-= <expr>"
      if (
        $assign->expression instanceof UnaryOpNode &&
        '-' === $assign->expression->op &&
        isset(self::$switch[$assign->op])
      ) {
        $assign= new AssignmentNode(array(
          'variable'   => $assign->variable,
          'expression' => $assign->expression->expression,
          'op'         => self::$switch[$assign->op]
        ));
      }

      // Not optimizable
      return $assign;
    }
  }
?>
