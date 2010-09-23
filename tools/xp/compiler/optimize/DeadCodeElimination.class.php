<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'xp.compiler.ast.RoutineNode',
    'xp.compiler.optimize.Optimization'
  );

  /**
   * Removes dead code
   *
   * Statements after an unconditional return
   * ----------------------------------------
   * <code>
   *   public void main(string[] $args) {
   *     Console::writeLine('Hello ', $args[0]);
   *     return;
   *     Console::writeLine('Goodbye');   // Unreachable code
   *   }
   * </code>
   *
   * @see      http://en.wikipedia.org/wiki/Dead_code_elimination
   */
  class DeadCodeElimination extends Object implements Optimization {
    
    /**
     * Return node this optimization works on
     *
     * @return  lang.XPClass<? extends xp.compiler.ast.Node>
     */
    public function node() {
      return XPClass::forName('xp.compiler.ast.RoutineNode');
    }
    
    /**
     * Optimize a given node
     *
     * @param   xp.compiler.ast.Node in
     * @param   xp.compiler.optimize.Optimizations optimizations
     * @param   xp.compiler.ast.Node optimized
     */
    public function optimize(xp·compiler·ast·Node $in, Optimizations $optimizations) {
      $method= cast($in, 'xp.compiler.ast.RoutineNode');

      // Search for return statement, then see if anything comes after it
      $s= sizeof($method->body);
      foreach ($method->body as $i => $statement) {
        if ($statement instanceof ReturnNode && $i < $s) {
          $method->body= array_slice($method->body, 0, $i+ 1);  // Include return
          break;
        }
      }
      return $method;
    }
  }
?>
