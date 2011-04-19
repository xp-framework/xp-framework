<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.types.Scope', 'xp.compiler.task.CompilationTask');

  /**
   * Represents the method scope
   *
   * @see     xp://xp.compiler.Scope
   */
  class TaskScope extends Scope {
    
    /**
     * Constructor
     *
     * @param   xp.compiler.task.CompilationTask task
     */
    public function __construct(CompilationTask $task) {
      parent::__construct();
      $this->task= $task;
    }
  }
?>
