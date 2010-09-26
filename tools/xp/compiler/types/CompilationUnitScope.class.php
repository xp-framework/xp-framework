<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.types.Scope');

  /**
   * Represents the compilation unit scope
   *
   * In the following example:
   * <code>
   *   import util.cmd.*;
   *
   *   abstract class Command implements Runnable {
   *
   *     public function toString() {
   *       return $this.getClassName();
   *     }
   *   }
   * </code>
   * ...this scope represents the import statement and the class
   * declaration.
   *
   * @see     xp://xp.compiler.ClassScope
   * @see     xp://xp.compiler.MethodScope
   */
  class CompilationUnitScope extends Scope {
  
  }
?>
