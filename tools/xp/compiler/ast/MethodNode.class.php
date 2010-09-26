<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.compiler.ast.RoutineNode');

  /**
   * Represents a method
   *
   */
  class MethodNode extends RoutineNode {
    public $returns   = NULL;
    public $extension = FALSE;

  }
?>
